<?php
include dirname(__FILE__).'/../Tools/IsInGetTools.php';
include dirname(__FILE__).'/../Tools/ValidateAndSecure.php';
include dirname(__FILE__).'/../vendor/autoload.php';
include dirname(__FILE__).'/../Process/sql.php';

SessionStarter();
if(!isset($_SESSION['IsAuth']) || is_bool($_SESSION['IsAuth']) && !$_SESSION['IsAuth']){
    header('Location: login.php');
}
$stmt = $pdo->prepare('UPDATE User SET IsTwoFactor = 0 WHERE id = :id');
$stmt->bindValue(':id', $_SESSION['UserId'], PDO::PARAM_INT);
$stmt->execute();
$title = 'Two-Factor Authorize Disabled';
$card_name = '2段階認証の無効化';
$message = '2段階認証の無効化が完了しました。';
$errtype = False;
if(array_key_exists('err', $_SESSION)){
    $errtype = True;
    $message = $_SESSION['err'];
    unset($_SESSION['err']);
}

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

$GAuthButton = '';

$option = '';


$scriptTo = 'JavaScript/Login.js';
$JS = '<script src="https://unpkg.com/jwt-decode/build/jwt-decode.js"></script>';

include dirname(__FILE__).'/../Template/BaseTemplate.php';
?>