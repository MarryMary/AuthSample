<?php
include dirname(__FILE__).'/Tools/IsInGetTools.php';
include dirname(__FILE__).'/Tools/ValidateAndSecure.php';

SessionStarter();

$title = 'Registration';
$card_name = '新規登録';
$message = '続行するにはメールアドレスを入力して下さい。';
$errtype = False;
if(array_key_exists('err', $_SESSION)){
    $errtype = True;
    $message = $_SESSION['err'];
    unset($_SESSION['err']);
}

$form = <<<EOF
<form action="Process/PreRegist.php" method="POST">
    <input type='email' name='email' class="form-control" placeholder='メールアドレス' style='margin-bottom: 3%;'>
    <div style="text-align: center;">
        <button type='submit' class='btn btn-primary' style="width: 80%;">登録</button>
    </div>
</form>
<br>
<div style="text-align: center;">
    <h2>または</h2>
</div>

EOF;

$option = <<<EOF
<p>アカウントをお持ちですか？<a href="login.php">ログイン</a></p>
<p>パスワードをお忘れですか？<a href="#">パスワードのリセット</a></p>
EOF;


$scriptTo = 'JavaScript/Login.js';

include dirname(__FILE__).'/Template/BaseTemplate.php';
