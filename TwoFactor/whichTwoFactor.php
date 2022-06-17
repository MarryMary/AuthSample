<?php
/*
* 2段階認証選択画面
*/
// 必要ファイルのインクルード
include dirname(__FILE__).'/../Tools/Session.php';
include dirname(__FILE__).'/../Tools/ValidateAndSecure.php';

//セッション開始
SessionStarter();

// ログイン状態かログインしていない場合
if(!SessionIsIn('IsAuth') || SessionIsIn('IsAuth') && SessionReader('IsAuth')){
    header("Location: /AuthSample/mypage.php");
}


$title = 'Two-Factor Authorize';
$card_name = '2段階認証';
$message = 'どちらの方法で2段階認証を行うか選択して下さい。';
$errtype = False;

$GAuthJS = '<link rel="stylesheet" href="/AuthSample/CSS/style.css">';

$form = <<<EOF
<div class="menu">
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
</div>
EOF;


$scriptTo = 'JavaScript/Login.js';
$JS = '<script src="https://unpkg.com/jwt-decode/build/jwt-decode.js"></script>';

include dirname(__FILE__).'/../Template/BaseTemplate.php';
