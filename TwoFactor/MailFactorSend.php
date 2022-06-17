<?php
/*
* メールでの2段階認証を選択した場合の画面
*/
// 必要ファイルのインクルード
include dirname(__FILE__).'/../Tools/Session.php';
include dirname(__FILE__).'/../Tools/ValidateAndSecure.php';
include dirname(__FILE__).'/../Tools/MailSender.php';
include dirname(__FILE__).'/../Tools/SQL.php';

// セッション開始
SessionStarter();

// もしログインしているか、ログインしていない場合
if(!SessionIsIn('IsAuth') || !SessionIsIn('NeedTwoFactor') || SessionIsIn('IsAuth') && is_bool(SessionReader('IsAuth')) && SessionReader('IsAuth')){
    header("location: /AuthSample/mypage.php");
}

// idを基準にデータを取得
$stmt = $pdo->prepare("SELECT * FROM User WHERE id = :id");
$stmt->bindValue(":id", $_SESSION["UserId"], PDO::PARAM_INT);
$res = $stmt->execute();

// SQLを正しく実行できなかった場合
if(!$res){
    header("Location: /AuthSample/Process/Logout.php");
// SQLを正しく実行できた場合
}else{
    // データ取得
    $data = $stmt->fetch();

    // データが存在しなかった場合
    if(is_bool($data)){
        header("Location: /AuthSample/Process/Logout.php");
    // データが存在する場合
    }else{
        // 2段階認証のトークンがセッションに無いかまたは再生成が必要な場合
        if(!SessionIsIn('2Factor-Token') || isset($_GET["regenerate"])){
            $str = 'abcdefghijklmnopqrstuvwxyz0123456789';
            $rand_str = substr(str_shuffle($str), 0, 6);
            $template = file_get_contents(dirname(__FILE__).'/../Template/CodeTemplate.html');
            $template = str_replace('{{TOKEN}}', $rand_str, $template);
            EmailSender($data["email"], "$SERVICE_NAME2段階認証コード", $template);
            SessionInsert('2Factor-Token', $rand_str);
        }

        $title = 'Two-Factor Authorize';
        $card_name = '2段階認証';
        $message = 'アカウントに連携されているメールアドレスに送信された2段階認証コードを入力して下さい。';
        $errtype = False;
        if(SessionIsIn('err')){
            $errtype = True;
            $message = SessionReader('err');
            SessionUnset('err');
        }


        $form = <<<EOF
<form action="EMailFactCheck.php" method="POST">
<input type='text' name='token' class="form-control" placeholder='2段階認証コード' style='margin-bottom: 3%;' maxlength="6">
<p><a href="MailFactorSend.php?regenerate=True">コードの再送信</a></p>
<div style="text-align: center;">
    <button type='button' class='btn btn-primary' onclick="history.back()" style="width: 40%;">＜＜戻る</button>
    <button type='submit' class='btn btn-success' style="width: 40%;">認証</button>
</div>
</form>
EOF;

        $scriptTo = 'JavaScript/Login.js';
        $JS = '<script src="https://unpkg.com/jwt-decode/build/jwt-decode.js"></script>';

        include dirname(__FILE__).'/../Template/BaseTemplate.php';
    }
}