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
    $stmt = $pdo->prepare("SELECT * FROM PreUser WHERE register_type = 1 AND user_token = :token");
    $stmt->bindValue(':token', $_GET["token"], PDO::PARAM_STR);
    $res = $stmt->execute();

    // 正常にSQLが実行できた場合
    if($res){
        // 1件取得
        $result = $stmt->fetch();
        //取得できた場合（条件一致が0件の場合はFalseになる）
        if(!is_bool($result)){
            // セッションにトークン情報とメールアドレスを代入
            SessionInsert('token', $_GET['token']);
            SessionInsert('email', $result['email']);
            // email変数にemail情報を代入
            $email = $result['email'];


            $title = 'Forget';
            $card_name = 'パスワードのリセット';
            $message = 'パスワードのリセットを行うには以下の情報を追加して下さい。';
            $errtype = False;
            if(SessionIsIn('err')){
                $errtype = True;
                $message = SessionReader('err');
                SessionUnset('err');
            }

            // フォーム作成
            $form = <<<EOF
<form action="/{$SERVICE_ROOT}/Process/PassForget.php" method="POST" enctype="multipart/form-data">
    <div class="mb-3">
        <input type='password' name='password1' class="form-control" placeholder='パスワード'>
        <div id="emailHelp" class="form-text">パスワードは8字以上16字以下で、「?、!、#、,」のいずれかの記号が入っている必要があります。</div>
    </div>
    <input type='password' name='password2' class="form-control" placeholder='パスワード(確認用)' style='margin-bottom: 3%;'>
    <div style="text-align: center; margin-top: 10px;">
        <button type='submit' class='btn btn-primary' style="width: 80%;">リセット</button>
    </div>
</form>

EOF;


            // JavaScript指定
            $scriptTo = '/'.$SERVICE_ROOT.'/JavaScript/Register.js';
            // cropper.js関連のJavaScriptを読み込み
            $JS = '<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.12/cropper.js" type="text/javascript"></script>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-cropper/1.0.1/jquery-cropper.js" type="text/javascript"></script>';

            // テンプレートファイルをインクルード
            include dirname(__FILE__) . '/../Template/BaseTemplate.php';
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