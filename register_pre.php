<?php
/*
 * 新規登録(仮新規登録)画面
 */
// 必要ファイルのインクルード
include 'Tools/Session.php';
include 'Tools/ValidateAndSecure.php';

// セッション開始
SessionStarter();

$title = 'Registration';
$card_name = '新規登録';
$message = '続行するにはメールアドレスを入力して下さい。';
$errtype = False;
if(SessionIsIn('err')){
    $errtype = True;
    $message = SessionReader('err');
    SessionUnset('err');
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
<p>パスワードをお忘れですか？<a href="forget.php">パスワードのリセット</a></p>
EOF;

//テンプレートファイルの読み込み
include dirname(__FILE__).'/Template/BaseTemplate.php';
