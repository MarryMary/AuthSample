<?php
include 'Tools/IsInGetTools.php';
include 'vendor/autoload.php';

use Endroid\QrCode\QrCode;

SessionStarter();
if(!isset($_SESSION['IsAuth']) || is_bool($_SESSION['IsAuth']) && !$_SESSION['IsAuth']){
    header('Location: login.php');
}

include 'Process/sql.php';
$stmt = $pdo->prepare("SELECT * FROM User WHERE id = :id");
$stmt->bindValue(":id", $_SESSION["UserId"], PDO::PARAM_STR);
$result = $stmt->execute();
if($result){
    $get = $stmt->fetch();
}

$ga = new PHPGangsta_GoogleAuthenticator();

if(is_null($get['TwoFactorSecret'])){
    $secret = $ga->createSecret();
    $stmt = $pdo->prepare("UPDATE User SET TwoFactorSecret = :secret, IsTwoFactor = 1 WHERE id = :id");
    $stmt->bindValue(":secret", $secret, PDO::PARAM_STR);
    $stmt->bindValue(":id", $_SESSION["UserId"], PDO::PARAM_STR);
    $result = $stmt->execute();
}else{
    $secret = $get['TwoFactorSecret'];
}

$qrCodeUrl = $ga->getQRCodeGoogleUrl($get['user_name'], $secret, 'HolyLive');
?>
<!DOCTYPE html>
<html lang='ja'>
<head>
    <meta charset='UTF-8'>
    <meta http-equiv='X-UA-Compatible' content='IE=edge'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>MyPage</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="CSS/style.css">
</head>
<body>
    <nav class="navbar navbar-light bg-light">
    <div class="container-fluid">
        <a class="navbar-brand" href="#">Sample</a>
    </div>
    </nav>
    <div class="container">
        <div class="glass">
            <div class="row">
                <div class="col-sm-3">
                    <div class="profile">
                        <img src="<?=$get['user_pict']?>" width="200" height="200" alt="user_profile">
                        <h2><?=$get['user_name']?></h2>
                        <button type="button" name="button" class="btn btn-primary" onclick="location.href='Process/Logout.php'" style="width: 90%; margin: 10px;">ログアウト</button>
                    </div>
                </div>
                <div class="col-sm-9">
                    <div class="menu">
                        <div style="text-align: center">
                            <h1>2段階認証の追加</h1>
                            <p>お使いのアカウントに2段階認証を追加します。</p>
                            <hr>
                            <div style="text-align: center">
                            <p>お使いのアカウントは2段階認証が設定可能です。<br>
                                Google Authenticatorアプリを使用するため、お使いのスマートフォンにインストールして下さい。
                            </p>
                            <p>以下のQRコードをGoogle Authenticatorアプリで読み込んで下さい。</p>
                            <img src="<?=$qrCodeUrl?>">
                            <p>また、GoogleAuthenticatorにアクセスできなくなってしまった場合は、以下のメールアドレスにワンタイムパスワードを送信します。</p>
                            <input type="text" class="form-control" value="<?=$get['email']?>" style="text-align: center" disabled>
                            <button type="button" class="btn btn-primary" style="width: 90%;margin-top: 10px;" onclick="location.href='mypage.php'">＜＜ホームに戻る</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
</body>
</html>