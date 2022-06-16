<?php
/*
 * 新規登録(仮新規登録)画面
 */
// 必要ファイルのインクルード
include dirname(__FILE__).'/Tools/IsInGetTools.php';
include dirname(__FILE__).'/Tools/ValidateAndSecure.php';

// セッション開始
SessionStarter();

$title = 'Registration';
$card_name = '新規登録';
$message = '続行するにはメールアドレスを入力して下さい。';
$errtype = False;
if(isset($_SESSION["err"])){
    $errtype = True;
    $message = $_SESSION['err'];
    unset($_SESSION['err']);
}

//フォーム作成
$form = <<<EOF
<form action="Process/PreRegist.php" method="POST">
    <input type='email' name='email' class="form-control" placeholder='メールアドレス' style='margin-bottom: 3%;'>
    <div style="text-align: center;">
        <button type='submit' class='btn btn-primary' style="width: 80%;">登録</button>
    </div>
</form>
<br>

EOF;

// オプションメニュー表示
$option = <<<EOF
<p>アカウントをお持ちですか？<a href="login.php">ログイン</a></p>
<p>パスワードをお忘れですか？<a href="#">パスワードのリセット</a></p>
EOF;

//テンプレートファイルの読み込み
include dirname(__FILE__).'/Template/BaseTemplate.php';
