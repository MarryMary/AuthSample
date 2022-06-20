<?php
/*
 * 新規登録(仮新規登録)画面
 */
// 必要ファイルのインクルード
include dirname(__FILE__).'/Tools/Session.php';
include dirname(__FILE__).'/Tools/ValidateAndSecure.php';

// セッション開始
SessionStarter();

$title = 'Forget';
$card_name = 'パスワードのリセット';
$message = 'パスワードをリセットするにはアカウントのメールアドレスを入力して下さい。';
$errtype = False;
if(SessionIsIn('err')){
    $errtype = True;
    $message = SessionReader('err');
    SessionUnset('err');
}

//フォーム作成
$form = <<<EOF
<form action="Process/PrePassReset.php" method="POST">
    <div class="mb-3">
        <input type='email' name='email' class="form-control" placeholder='メールアドレス'>
        <div id="emailHelp" class="form-text">Googleアカウントで登録された方は、Googleアカウントのメールアドレスを入力して下さい。</div>
    </div>
    <div style="text-align: center;">
        <button type='submit' class='btn btn-primary' style="width: 80%;">申請</button>
    </div>
</form>
<br>

EOF;

// オプションメニュー表示
$option = <<<EOF
<p>アカウントをお持ちですか？<a href="login.php">ログイン</a></p>
<p>アカウントをお持ちではありませんか？<a href="register_pre.php">新規登録</a></p>
EOF;

//テンプレートファイルの読み込み
include dirname(__FILE__).'/Template/BaseTemplate.php';
