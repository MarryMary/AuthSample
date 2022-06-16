<?php
include dirname(__FILE__).'/../Tools/IsInGetTools.php';
include dirname(__FILE__).'/../Tools/ValidateAndSecure.php';
include dirname(__FILE__).'/../Tools/MailSender.php';
include dirname(__FILE__).'/../Process/sql.php';


SessionStarter();

if(!isset($_SESSION["IsAuth"]) || !isset($_SESSION["NeedTwoFactor"]) || isset($_SESSION["IsAuth"]) && is_bool($_SESSION["IsAuth"]) && $_SESSION["IsAuth"]){
    header("location: /AuthSample/mypage.php");
}else{
    $title = 'Two-Factor Authorize';
    $card_name = '2段階認証';
    $message = 'Google Authenticatorアプリに表示されている2段階認証コードを入力して下さい。';
    $errtype = False;
    if(array_key_exists('err', $_SESSION)){
        $errtype = True;
        $message = $_SESSION['err'];
        unset($_SESSION['err']);
    }

    $GAuthJS = '';

    $form = <<<EOF
<form action="GAFactCheck.php" method="POST">
<input type='text' name='token' class="form-control" placeholder='2段階認証コード' style='margin-bottom: 3%;' maxlength="6">
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