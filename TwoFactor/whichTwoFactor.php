<?php
include 'Tools/IsInGetTools.php';
SessionStarter();
if(!isset($_SESSION["IsAuth"]) || isset($_SESSION["IsAuth"]) && is_bool($_SESSION["IsAuth"]) && $_SESSION["IsAuth"] && !isset($_SESSION["NeedTwoFactor"])){
    header("location: /AuthSample/login.php");
}
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
            <div class="menu">
                <div style="text-align: center">
                    <h1>2段階認証オプション</h1>
                    <p>どちらの方法で2段階認証を実施しますか？</p>
                    <hr>
                    <a href="/AuthSample/TwoFactor/GoogleAuthenticator.php">
                        <div class="selector">
                            <p>Google Authenticator</p>
                            <small>Google Authenticatorアプリを使用して2段階認証を行います。</small>
                        </div>
                        <hr>
                    </a>
                    <a href="/AuthSample/TwoFactor/MailFactorSend.php">
                        <div class="selector">
                            <p>メール送信</p>
                            <small>アカウントに連携されているメールアドレスにワンタイムパスワードを送信して2段階認証を行います。</small>
                        </div>
                        <hr>
                    </a>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
</body>
</html>