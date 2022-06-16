<?php
include dirname(__FILE__).'/../Tools/IsInGetTools.php';
include dirname(__FILE__).'/../vendor/autoload.php';
include dirname(__FILE__).'/../Tools/ValidateAndSecure.php';
include dirname(__FILE__).'/../Tools/MailSender.php';
include dirname(__FILE__).'/../Process/sql.php';


SessionStarter();

if(!isset($_GET["token"])){
    header("location: /AuthSample/mypage.php");
}


$stmt = $pdo->prepare("SELECT * FROM PreUser WHERE user_token = :token");
$stmt->bindValue(":token", $_GET["token"], PDO::PARAM_STR);
$res = $stmt->execute();
$show_code = False;
if(isset($_GET["cant_read"])){
    $show_code = True;
}else{
    $show_code = False;
}
if(!$res){
    header("Location: /AuthSample/Process/Logout.php");
}else{
    $data = $stmt->fetch();
    if(is_bool($data)){
        header("Location: /AuthSample/Process/Logout.php");
    }else{
        $ga = new PHPGangsta_GoogleAuthenticator();
        $secret = $ga->createSecret();
        $_SESSION["token"] = $_GET["token"];
        $stmt = $pdo->prepare("UPDATE User SET TwoFactorSecret = :secret, IsTwoFactor = 1 WHERE id = :id");
        $stmt->bindValue(":secret", $secret, PDO::PARAM_STR);
        $stmt->bindValue(":id", $_SESSION["UserId"], PDO::PARAM_STR);
        $result = $stmt->execute();
        $stmt = $pdo->prepare("SELECT * FROM User WHERE id = :id");
        $stmt->bindValue(":id", $_SESSION["UserId"], PDO::PARAM_STR);
        $result = $stmt->execute();
        if($result){
            $get = $stmt->fetch();
        }else{
            $get = False;
        }
        $title = 'Two-Factor Authorize Enabled';
        $card_name = '2段階認証の有効化';
        $message = 'Google Authenticatorアプリを有効化する';
        $ga = new PHPGangsta_GoogleAuthenticator();
        $qrCodeUrl = $ga->getQRCodeGoogleUrl($get['user_name'], $secret, 'HolyLive');
        $errtype = False;
        $token = trim($_GET["token"]);
        if(array_key_exists('err', $_SESSION)){
            $errtype = True;
            $message = $_SESSION['err'];
            unset($_SESSION['err']);
        }

        $GAuthJS = '';
        if(is_bool($get)){
            $form = <<<EOF
<p style="color: red;">
    問題が発生しました。
</p>
<div style="text-align: center;">
    <button type="button" class="btn btn-primary" onclick="location.href='/AuthSample/TwoFactorAuthorize.php'" style="width: 90%;">設定画面へ</button>
</div>
EOF;
        }else if($show_code) {
            $form = <<<EOF
<p>
    メールの有効性が確認できたため、2段階認証が有効化されました。<br>
    引き続きGoogle Authenticatorアプリに本サイトを同期させるには、アプリで以下のコードを入力して同期させて下さい。
</p>
<div style="text-align: center;">
    <h2>{$secret}</h2>
</div>
<a href="EnableTwoFactor.php?token={$token}">QRコードを表示</a>
<p>
    アプリに表示されている6桁のコードを以下に入力して送信して下さい。
</p>
<form action="GAppEnable.php" method="POST">
<input type='text' name='token' class="form-control" placeholder='2段階認証コード' style='margin-bottom: 3%;' maxlength="6">
<div style="text-align: center;">
<button type='button' class='btn btn-primary' onclick="history.back()" style="width: 40%;">今は設定しない</button>
<button type='submit' class='btn btn-success' style="width: 40%;">送信</button>
</div>
</form>
EOF;
        }else{
            $form = <<<EOF
<p>
    メールの有効性が確認できたため、2段階認証が有効化されました。<br>
    引き続きGoogle Authenticatorアプリに本サイトを同期させるには、アプリで以下のQRコードを読み込んで同期させて下さい。
</p>
<div style="text-align: center;">
    <img src="{$qrCodeUrl}">
</div>
<a href="EnableTwoFactor.php?cant_read=True&token={$token}">QRコードが読み取れない場合</a>
<p>
    アプリに表示されている6桁のコードを以下に入力して送信して下さい。
</p>
<form action="GAppEnable.php" method="POST">
<input type='text' name='token' class="form-control" placeholder='2段階認証コード' style='margin-bottom: 3%;' maxlength="6">
<div style="text-align: center;">
<button type='button' class='btn btn-primary' onclick="history.back()" style="width: 40%;">今は設定しない</button>
<button type='submit' class='btn btn-success' style="width: 40%;">送信</button>
</div>
</form>
EOF;
        }

        $GAuthButton = '';

        $option = '';


        $scriptTo = 'JavaScript/Login.js';
        $JS = '<script src="https://unpkg.com/jwt-decode/build/jwt-decode.js"></script>';

        include dirname(__FILE__).'/../Template/BaseTemplate.php';
    }
}