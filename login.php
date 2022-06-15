<?php
include dirname(__FILE__).'/Tools/IsInGetTools.php';
include dirname(__FILE__).'/Tools/ValidateAndSecure.php';

SessionStarter();

if(isset($_SESSION["IsAuth"])){
    header("Location: mypage.php");
}

$title = 'Login';
$card_name = 'ログイン';
$message = '続行するにはログインしてください。';
$errtype = False;
if(array_key_exists('err', $_SESSION)){
    $errtype = True;
    $message = $_SESSION['err'];
    unset($_SESSION['err']);
}

$GAuthJS = '<script src="https://accounts.google.com/gsi/client" async defer></script><div id="g_id_onload" data-client_id="345840626602-q37bp5di0lrr53n3bar423uhg90rff67.apps.googleusercontent.com" data-callback="AuthorizeStart"></div>';

$form = <<<EOF
<form action="Process/Auth.php" method="POST">
    <input type='email' name='email' class="form-control" placeholder='メールアドレス' style='margin-bottom: 3%;'>
    <input type='password' name='password' class="form-control" placeholder='パスワード' style='margin-bottom: 3%;'>
    <div style="text-align: center;">
        <button type='submit' class='btn btn-primary' style="width: 80%;">ログイン</button>
    </div>
</form>
<br>
<div style="text-align: center;">
    <h2>または</h2>
</div>

EOF;

$GAuthButton = <<<EOF
<br>
<div class="g_id_signin"
     data-type="standard"
     data-size="large"
     data-theme="outline"
     data-text="sign_in_with"
     data-shape="rectangular"
     data-logo_alignment="left">
</div>
EOF;

$option = <<<EOF
<p>アカウントをお持ちではありませんか？<a href="register_pre.php">新規登録</a></p>
<p>パスワードをお忘れですか？<a href="#">パスワードのリセット</a></p>
EOF;


$scriptTo = 'JavaScript/Login.js';
$JS = '<script src="https://unpkg.com/jwt-decode/build/jwt-decode.js"></script>';

include dirname(__FILE__).'/Template/BaseTemplate.php';
