<?php
include dirname(__FILE__).'/Tools/IsInGetTools.php';
include dirname(__FILE__).'/Tools/ValidateAndSecure.php';

SessionStarter();

$title = 'Login';
$card_name = 'ログイン';
$message = '続行するにはログインしてください。';
$errtype = False;
if(array_key_exists('err', $_SESSION)){
    $errtype = True;
    $message = $_SESSION['err'];
    unset($_SESSION['err']);
}

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

$option = <<<EOF
<p>アカウントをお持ちではありませんか？<a href="#">新規登録</a></p>
<p>パスワードをお忘れですか？<a href="#">パスワードのリセット</a></p>
EOF;


$scriptTo = 'JavaScript/Login.js';

include dirname(__FILE__).'/Template/BaseTemplate.php';
