<?php
include dirname(__FILE__).'/../Tools/IsInGetTools.php';
include dirname(__FILE__).'/Tools/ValidateAndSecure.php';
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
        if(!isset($_SESSION["2Factor-Token"])){
            $str = 'abcdefghijklmnopqrstuvwxyz0123456789';
            $rand_str = substr(str_shuffle($str), 0, 6);
            $template = file_get_contents('CodeTemplate.html');
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

        $GAuthJS = '<script src="https://accounts.google.com/gsi/client" async defer></script>
        <div id="g_id_onload" data-client_id="345840626602-q37bp5di0lrr53n3bar423uhg90rff67.apps.googleusercontent.com" data-callback="AuthorizeStart"></div>';

        $form = <<<EOF
<form action="Process/TwoFactor/EMailFactCheck.php" method="POST">
<input type='text' name='token' class="form-control" placeholder='2段階認証コード' style='margin-bottom: 3%;' maxlength="6">
<div style="text-align: center;">
    <button type='submit' class='btn btn-primary' style="width: 80%;">認証</button>
</div>
</form>
EOF;

        $GAuthButton = '';

        $option = '';


        $scriptTo = 'JavaScript/Login.js';
        $JS = '<script src="https://unpkg.com/jwt-decode/build/jwt-decode.js"></script>';

        include dirname(__FILE__).'/Template/BaseTemplate.php';
    }
}