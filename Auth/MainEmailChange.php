<?php
/*
 * 新規登録の本登録（メールアドレスの有効性が確認できた場合）
 */
// 必要ファイルのインクルード
include dirname(__FILE__).'/../Tools/Session.php';
include dirname(__FILE__).'/../Tools/ValidateAndSecure.php';
include dirname(__FILE__).'/../Tools/SQL.php';
include dirname(__FILE__).'/../Template/ServiceData.php';

// セッション開始
SessionStarter();

// トークンが送信されている場合
if(isset($_GET["token"])){
    // 24時間以上経過しているデータを物理削除
    $stmt = $pdo->prepare("delete from PreUser WHERE register_at<=sysdate() - interval 1 day");
    $stmt->execute();

    // そのトークンを持ったアカウントを検索(UUID V4)
    $stmt = $pdo->prepare("SELECT * FROM PreUser WHERE register_type = 2 AND user_token = :token");
    $stmt->bindValue(':token', $_GET["token"], PDO::PARAM_STR);
    $res = $stmt->execute();

    // 正常にSQLが実行できた場合
    if($res){
        // 1件取得
        $result = $stmt->fetch();
        //取得できた場合（条件一致が0件の場合はFalseになる）
        if(!is_bool($result)){
            $email = $result['email'];
            $id = $result['affect_id'];
            $stmt = $pdo->prepare("UPDATE User SET email = :email WHERE id = :id");
            $stmt->bindParam(':email', $email, PDO::PARAM_STR);
            $stmt->bindParam(':id', $id, PDO::PARAM_STR);
            $res = $stmt->execute();

            // SQLが正しく実行できた場合
            if ($res) {
                // 仮ユーザーテーブルから今回の情報を削除
                $stmt = $pdo->prepare("DELETE FROM PreUser WHERE user_token = :user_token");
                $stmt->bindParam(':user_token', $_GET["token"], PDO::PARAM_STR);
                $res = $stmt->execute();
            // SQLが正しく実行できなかった場合
            } else {
                header('Location: /'.$SERVICE_ROOT.'/Auth/login.php');
            }


            $title = 'Update Completed';
            $card_name = 'メールアドレスの更新完了';
            $message = 'メールアドレスの更新が完了しました。';
            $errtype = False;
            if(SessionIsIn('err')){
                $errtype = True;
                $message = SessionReader('err');
                SessionUnset('err');
            }

            // フォーム作成
            $form = <<<EOF
<p>
    メールアドレスの更新が完了しました。<br>
    次回ログイン時からは新しいメールアドレスでログインして下さい。<br>
    メールアドレスが変わってもGoogleでのシングルサインオンには影響がありませんのでご安心下さい。
</p>
<div style="text-align:center;">
    <button type="button" class="btn btn-primary" onclick="location.href='/{$SERVICE_ROOT}/index.php'" style="width: 90%;">ホームへ戻る</button>
</div>

EOF;
            // テンプレートファイルをインクルード
            include dirname(__FILE__).'/../Template/BaseTemplate.php';
        }else{
            //データベースに情報がなかった場合
            header('Location: /'.$SERVICE_ROOT.'/Auth/login.php');
        }
    }else{
        // 正常にデータベースへのSQLが実行できなかった場合
        header('Location: /'.$SERVICE_ROOT.'/Auth/login.php');
    }
}else{
    //トークンが送信されていなかった場合
    header('Location: /'.$SERVICE_ROOT.'/Auth/login.php');
}