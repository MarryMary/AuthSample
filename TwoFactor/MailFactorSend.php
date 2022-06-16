<?php
include dirname(__FILE__).'/../Tools/IsInGetTools.php';
include dirname(__FILE__).'/../Tools/ValidateAndSecure.php';
include dirname(__FILE__).'/../Tools/MailSender.php';
include dirname(__FILE__).'/../Process/sql.php';


SessionStarter();

if(!isset($_SESSION["IsAuth"]) || !isset($_SESSION["NeedTwoFactor"]) || isset($_SESSION["IsAuth"]) && is_bool($_SESSION["IsAuth"]) && $_SESSION["IsAuth"]){
    header("location: /AuthSample/mypage.php");
}

$stmt = $pdo->prepare("SELECT * FROM User WHERE id = :id");
$stmt->bindValue(":id", $_SESSION["UserId"], PDO::PARAM_INT);
$res = $stmt->execute();
if(!$res){
    header("Location: /AuthSample/Process/Logout.php");
}else{
    $data = $stmt->fetch();
    if(is_bool($data)){
        header("Location: /AuthSample/Process/Logout.php");
    }else{
        if(!isset($_SESSION["2Factor-Token"]) || isset($_GET["regenerate"])){
            $str = 'abcdefghijklmnopqrstuvwxyz0123456789';
            $rand_str = substr(str_shuffle($str), 0, 6);
            $template = file_get_contents(dirname(__FILE__).'/../Template/CodeTemplate.html');
            $template = str_replace('{{TOKEN}}', $rand_str, $template);
            EmailSender($data["email"], "HolyLive2段階認証コード", $template);
            $_SESSION["2Factor-Token"] = $rand_str;
        }

        $title = 'Two-Factor Authorize';
        $card_name = '2段階認証';
        $message = 'アカウントに連携されているメールアドレスに送信された2段階認証コードを入力して下さい。';
        $errtype = False;
        if(array_key_exists('err', $_SESSION)){
            $errtype = True;
            $message = $_SESSION['err'];
            unset($_SESSION['err']);
        }

        $GAuthJS = '';

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

        $GAuthButton = '';

        $option = '';


        $scriptTo = 'JavaScript/Login.js';
        $JS = '<script src="https://unpkg.com/jwt-decode/build/jwt-decode.js"></script>';

        include dirname(__FILE__).'/../Template/BaseTemplate.php';
    }
}