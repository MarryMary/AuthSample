<?php
/*
 * ログイン画面本体
 */

// 必要ファイルのインクルード
include dirname(__FILE__).'/Tools/Session.php';
include dirname(__FILE__).'/Tools/ValidateAndSecure.php';

// セッション開始
SessionStarter();

// セッションが消える前にエラーを保存
$message = '続行するにはログインしてください。';
$errtype = False;
if(SessionIsIn('err')){
    $errtype = True;
    $message = SessionReader('err');
    SessionUnset('err');
}

// アカウント復元フラグが立っている場合
if(SessionIsIn('Recover') && SessionIsIn('UserId')){
    // セッション情報を削除
    SessionUnset();
// ログイン状態の場合はmypage.phpへ推移
}elseif(SessionIsIn('IsAuth') && SessionReader('IsAuth')){
    header("Location: mypage.php");
//ログインはしているがIsAuthがFalse(2段階認証未実施)の場合はwhichTwoFactor.phpに遷移
}elseif(SessionIsIn('IsAuth') && !SessionReader('IsAuth')){
    header("Location: TwoFactor/whichTwoFactor.php");
}

$title = 'Login';
$card_name = 'ログイン';
$address = '';
if(isset($_COOKIE['SavedAuthAddress'])){
    $address = $_COOKIE['SavedAuthAddress'];
}

// Googleシングル・サインオン用のJavaScriptを読み込み
$GAuthJS = '<script src="https://accounts.google.com/gsi/client" async defer></script><div id="g_id_onload" data-client_id="345840626602-q37bp5di0lrr53n3bar423uhg90rff67.apps.googleusercontent.com" data-callback="AuthorizeStart"></div>';

// フォーム作成
$form = <<<EOF
<form action="Process/Auth.php" method="POST">
    <input type='email' name='email' class="form-control" placeholder='メールアドレス' value="{$address}">
    <div class="form-check" style='margin-bottom: 3%;'>
        <input class="form-check-input" type="checkbox" value="SaveAddress" id="SaveAddress">
        <label class="form-check-label" for="SaveAddress">
            ログイン情報を保存する
        </label>
    </div>
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

//Googleでログインボタンを作成
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

// オプションメニュー作成
$option = <<<EOF
<p>アカウントをお持ちではありませんか？<a href="register_pre.php">新規登録</a></p>
<p>パスワードをお忘れですか？<a href="forget.php">パスワードのリセット</a></p>
EOF;


//JavaScript指定
$scriptTo = 'JavaScript/Login.js';
$JS = '<script src="https://unpkg.com/jwt-decode/build/jwt-decode.js"></script>';

// テンプレートファイルをインクルード
include dirname(__FILE__).'/Template/BaseTemplate.php';
