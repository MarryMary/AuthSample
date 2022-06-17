<?php
/*
* 2段階認証の無効化処理
*/
// 必要ファイルのインクルード
include dirname(__FILE__).'/../Tools/Session.php';
include dirname(__FILE__).'/../Tools/ValidateAndSecure.php';
include dirname(__FILE__).'/../vendor/autoload.php';
include dirname(__FILE__).'/../Tools/SQL.php';
include dirname(__FILE__).'/../Template/ServiceData.php';

// セッション開始
SessionStarter();

// ログインしていない場合
if(!SessionIsIn('IsAuth') || is_bool(SessionReader('IsAuth')) && !SessionReader('IsAuth')){
    header('Location: /$SERVICE_ROOT/login.php');
}

// 無効化フラグをインサート
$stmt = $pdo->prepare('UPDATE User SET IsTwoFactor = 0 WHERE id = :id');
$stmt->bindValue(':id', $_SESSION['UserId'], PDO::PARAM_INT);
$stmt->execute();


$title = 'Two-Factor Authorize Disabled';
$card_name = '2段階認証の無効化';
$message = '2段階認証の無効化が完了しました。';
$errtype = False;

$GAuthJS = '';
$form = <<<EOF
<p>
お使いのアカウントの2段階認証の無効化が完了しました。<br>
次回ログインから2段階認証が不要になります。<br>
</p>
<div style="text-align: center;">
    <button type="button" class="btn btn-primary" onclick="location.href='/AuthSample/TwoFactorAuthorize.php'" style="width: 90%;">戻る</button>
</div>
EOF;


$scriptTo = 'JavaScript/Login.js';
$JS = '<script src="https://unpkg.com/jwt-decode/build/jwt-decode.js"></script>';

include dirname(__FILE__).'/../Template/BaseTemplate.php';
?>