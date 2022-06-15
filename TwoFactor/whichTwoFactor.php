<?php
include dirname(__FILE__).'/Tools/IsInGetTools.php';
include dirname(__FILE__).'/Tools/ValidateAndSecure.php';

SessionStarter();

if(isset($_SESSION["IsAuth"])){
    header("Location: mypage.php");
}

$title = 'Two-Factor Authorize';
$card_name = '2段階認証';
$message = 'どちらの方法で2段階認証を行うか選択して下さい。';
$errtype = False;
if(array_key_exists('err', $_SESSION)){
    $errtype = True;
    $message = $_SESSION['err'];
    unset($_SESSION['err']);
}

$GAuthJS = '<script src="https://accounts.google.com/gsi/client" async defer></script>
<div id="g_id_onload" data-client_id="345840626602-q37bp5di0lrr53n3bar423uhg90rff67.apps.googleusercontent.com" data-callback="AuthorizeStart"></div>
<link rel="stylesheet" href="/AuthSample/CSS/style.css">';

$form = <<<EOF
<a href="/AuthSample/TwoFactor/GoogleAuthenticator.php">
<div class="selector">
    <p>Google Authenticator</p>
    <small>Google Authenticatorアプリを使用して2段階認証を行います。</small>
</div>
<hr>
</a>
<a href="/AuthSample/TwoFactor/MailFactorSend.php">
<div class="selector">
    <p>メール送信</p>
    <small>アカウントに連携されているメールアドレスにワンタイムパスワードを送信して2段階認証を行います。</small>
</div>
<hr>
</a>

EOF;

$GAuthButton = '';

$option = '';


$scriptTo = 'JavaScript/Login.js';
$JS = '<script src="https://unpkg.com/jwt-decode/build/jwt-decode.js"></script>';

include dirname(__FILE__).'/Template/BaseTemplate.php';
