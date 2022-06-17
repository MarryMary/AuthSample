<?php
/*
 * 新規登録の本登録（メールアドレスの有効性が確認できた場合）
 */
// 必要ファイルのインクルード
include 'Tools/Session.php';
include 'Tools/ValidateAndSecure.php';
include 'Tools/SQL.php';

// セッション開始
SessionStarter();

// トークンが送信されている場合
if(isset($_GET["token"])){
    // 24時間以上経過しているデータを物理削除
    $stmt = $pdo->prepare("delete from PreUser WHERE register_at<=sysdate() - interval 1 day");
    $stmt->execute();

    // そのトークンを持ったアカウントを検索(UUID V4)
    $stmt = $pdo->prepare("SELECT * FROM PreUser WHERE register_type = 1 AND user_token = :token");
    $stmt->bindValue(':token', $_GET["token"], PDO::PARAM_STR);
    $res = $stmt->execute();

    // 正常にSQLが実行できた場合
    if($res){
        // 1件取得
        $result = $stmt->fetch();
        //取得できた場合（条件一致が0件の場合はFalseになる）
        if(!is_bool($result)){
            $stmt = $pdo->prepare("UPDATE User SET pass = :password WHERE id = :id");
            $stmt->bindParam(':password', $password, PDO::PARAM_STR);
            $stmt->bindParam(':id', $data['id'], PDO::PARAM_STR);
            $res = $stmt->execute();

            // SQLが正しく実行できた場合
            if ($res) {
                // 仮ユーザーテーブルから今回の情報を削除
                $stmt = $pdo->prepare("DELETE FROM PreUser WHERE user_token = :user_token");
                $stmt->bindParam(':user_token', $token, PDO::PARAM_STR);
                $res = $stmt->execute();

                // 登録完了フラグを立ててリセット完了画面へ遷移
                SessionInsert('registration', True);
                header('Location: /AuthSample/ResetFinish.php');
                // SQLが正しく実行できなかった場合
            } else {
                header('Location: /AuthSample/login.php');
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

EOF;


            // JavaScript指定
            $scriptTo = 'JavaScript/Register.js';
            // cropper.js関連のJavaScriptを読み込み
            $JS = '<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.12/cropper.js" type="text/javascript"></script>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-cropper/1.0.1/jquery-cropper.js" type="text/javascript"></script>';

            // テンプレートファイルをインクルード
            include dirname(__FILE__).'/Template/BaseTemplate.php';
        }else{
            //データベースに情報がなかった場合
            header('Location: /AuthSample/login.php');
        }
    }else{
        // 正常にデータベースへのSQLが実行できなかった場合
        header('Location: /AuthSample/login.php');
    }
}else{
    //トークンが送信されていなかった場合
    header('Location: /AuthSample/login.php');
}