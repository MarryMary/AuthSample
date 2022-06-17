<?php
/*
 * ユーザー名変更画面
 */
// 必要ファイルのインクルード
include dirname(__FILE__).'/../Tools/Session.php';
include dirname(__FILE__).'/../vendor/autoload.php';
include dirname(__FILE__).'/../Tools/SQL.php';

// セッション開始
SessionStarter();

// もしもログインしていないか2段階認証未実施の場合はログイン画面に遷移
if(!SessionIsIn('IsAuth') || is_bool(SessionReader('IsAuth')) && !SessionReader('IsAuth')){
    header('Location: login.php');
}

// ユーザー情報検索
$stmt = $pdo->prepare("SELECT * FROM User WHERE id = :id");
$stmt->bindValue(":id", $_SESSION["UserId"], PDO::PARAM_STR);
$result = $stmt->execute();
//もしユーザー情報があれば取得
if($result){
    $get = $stmt->fetch();
}else{
    // なければマイページに遷移
    header("Location: mypage.php");
}

$GLinked = False;
$title = 'Googleアカウントとの連携';
$message = 'あなたのアカウントをGoogleアカウントと連携します。';
if(!is_null($get['GAuthID'])){
    $title = 'Googleアカウントとの連携解除';
    $message = 'あなたのアカウントとGoogleアカウントとの連携を解除します。';
    $GLinked = True;
}

$errmode = False;

if(SessionIsIn('err')){
    $errmode = True;
    $message = SessionReader('err');
    SessionUnset('err');
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
    <link rel="stylesheet" href="/AuthSample/CSS/style.css">
    <?php
        if(!$GLinked):
    ?>
    <script src="https://accounts.google.com/gsi/client" async defer></script><div id="g_id_onload" data-client_id="345840626602-q37bp5di0lrr53n3bar423uhg90rff67.apps.googleusercontent.com" data-callback="AuthorizeStart"></div>
    <?php
        endif;
    ?>
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
                    <button type="button" name="button" class="btn btn-primary" onclick="location.href='/AuthSample/Process/Logout.php'" style="width: 90%; margin: 10px;">ログアウト</button>
                </div>
            </div>
            <div class="col-sm-9">
                <div class="menu">
                    <button type="button" class="btn btn-primary" style="width: 40%;margin-top: 10px;" onclick="location.href='/AuthSample/mypage.php'">＜＜ホームに戻る</button>
                    <br>
                    <div style="text-align: center">
                        <h1><?=$title?></h1>
                        <p style='color:<?= $errmode ? 'red' : 'black' ?>'><?=$message?></p>
                        <hr>
                        <?php
                            if($GLinked):
                        ?>
                        <p>
                            あなたのアカウントは現在Googleアカウントと関連付いています。<br>
                            Googleアカウントとの関連付けを解除するには以下のボタンを押して下さい。<br>
                            また、現在のメールアドレスがGoogleアカウントのものと同じであり、Googleアカウントが現在も存在する場合は、ログイン画面でGoogleでログインを選択すると再度関連付いてしまいますのでご注意下さい。
                        </p>
                        <div style="text-align: center;">
                            <button type="button" class="btn btn-danger" onclick="location.href='/AuthSample/Process/UnLinkGAuth.php'" style="width: 90%;">関連付け解除</button>
                        </div>
                        <?php
                            else:
                        ?>
                                <p>
                                    あなたのアカウントは現在Googleアカウントと関連付いていません。<br>
                                    Googleアカウントとの関連付けを行うには以下のボタンを押して下さい。
                                </p>
                                <br>
                                <div style="text-align: center;">
                                    <div class="g_id_signin"
                                         data-type="standard"
                                         data-size="large"
                                         data-theme="outline"
                                         data-text="sign_in_with"
                                         data-shape="rectangular"
                                         data-logo_alignment="left">
                                    </div>
                                </div>
                        <?php
                            endif;
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://unpkg.com/jwt-decode/build/jwt-decode.js"></script>
<script src="/AuthSample/JavaScript/LinkG.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
</body>
</html>