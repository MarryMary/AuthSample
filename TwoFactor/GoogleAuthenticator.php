<?php
/*
* Google Authenticatorを使用したログインページ
*/
// 必要ファイルのインクルード
include dirname(__FILE__).'/../Tools/Session.php';
include dirname(__FILE__).'/../Tools/ValidateAndSecure.php';
include dirname(__FILE__).'/../Tools/MailSender.php';
include dirname(__FILE__).'/../Tools/SQL.php';
include dirname(__FILE__).'/../Template/ServiceData.php';

// セッション開始
SessionStarter();

// ログイン状態であるか、ログインしていない場合
if(!SessionIsIn('IsAuth') || !SessionIsIn('NeedTwoFactor') || SessionIsIn('IsAuth') && is_bool(SessionReader('IsAuth')) && Sessionreader('IsAuth')){
    header("location: /$SERVICE_ROOT/mypage.php");
// 2段階認証が必要な場合
}else{

    $title = 'Two-Factor Authorize';
    $card_name = '2段階認証';
    $message = 'Google Authenticatorアプリに表示されている2段階認証コードを入力して下さい。';
    $errtype = False;
    if(SessionIsIn('err')){
        $errtype = True;
        $message = SessionReader('err');
        SessionUnset('err');
    }

    $form = <<<EOF
<form action="GAFactCheck.php" method="POST">
<input type='text' name='token' class="form-control" placeholder='2段階認証コード' style='margin-bottom: 3%;' maxlength="6">
<div style="text-align: center;">
<button type='button' class='btn btn-primary' onclick="history.back()" style="width: 40%;">＜＜戻る</button>
<button type='submit' class='btn btn-success' style="width: 40%;">認証</button>
</div>
</form>
EOF;


    $scriptTo = 'JavaScript/Login.js';
    $JS = '<script src="https://unpkg.com/jwt-decode/build/jwt-decode.js"></script>';

    include dirname(__FILE__).'/../Template/BaseTemplate.php';
}