<?php
/*
 * 2段階認証設定画面
 */
// 必要ファイルのインクルード
include 'Tools/Session.php';
include 'vendor/autoload.php';
include 'Tools/SQL.php';
include 'Template/ServiceData.php';

// セッション開始
SessionStarter();

// もしもログインしていないか2段階認証未実施の場合はログイン画面に遷移
if(!SessionIsIn('IsAuth') || is_bool(SessionReader('IsAuth')) && !SessionReader('IsAuth')){
    header('Location: login.php');
}

// ユーザー情報検索
$userid = SessionReader('UserId');
$stmt = $pdo->prepare("SELECT * FROM User WHERE id = :id");
$stmt->bindValue(":id", $userid, PDO::PARAM_STR);
$result = $stmt->execute();

//もしユーザー情報があれば取得
if($result){
    $get = $stmt->fetch();
}else{
    // なければマイページに遷移
    header("Location: mypage.php");
}


// GoogleAuthenticatorクラスをインスタンス化
$ga = new PHPGangsta_GoogleAuthenticator();

// 2段階認証が有効な状態である場合（=2段階認証のシークレットキーも存在する）
if($get['IsTwoFactor'] == 1){
    // シークレットキー取得
    $secret = $get['TwoFactorSecret'];
}else{
    // シークレットキーがない場合、空文字
    $secret = '';
}

// QRコードを生成
$qrCodeUrl = $ga->getQRCodeGoogleUrl($get['user_name'], $secret, $SERVICE_NAME);
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
                        <button type="button" class="btn btn-primary" style="width: 40%;margin-top: 10px;" onclick="location.href='mypage.php'">＜＜ホームに戻る</button>
                        <br>
                        <div style="text-align: center">
                            <h1>2段階認証の追加</h1>
                            <p>お使いのアカウントに2段階認証を追加します。</p>
                            <hr>
                            <?php
                                // 2段階認証未設定の場合
                                if($get['IsTwoFactor'] == 0):
                            ?>
                                <p>
                                    お使いのアカウントは2段階認証を設定可能です。<br>
                                    2段階認証の利用にはGoogle Authenticatorアプリが必要です。<br>
                                    以下からダウンロードして下さい。
                                </p>
                                <a href="https://apps.apple.com/us/app/google-authenticator/id388497605?itsct=apps_box_badge&amp;itscg=30200" style="display: inline-block; overflow: hidden; border-radius: 13px; width: 250px; height: 83px;"><img src="https://tools.applemediaservices.com/api/badges/download-on-the-app-store/black/en-us?size=250x83&amp;releaseDate=1284940800&h=7fc6b39acc8ae5a42ad4b87ff8c7f88d" alt="Download on the App Store" style="border-radius: 13px; width: 230px; height: 83px;"></a>
                                <a href="https://play.google.com/store/apps/details?id=com.google.android.apps.authenticator2" style="display: inline-block; overflow: hidden; border-radius: 13px; width: 250px; height: 83px;"><img src="Resources/google-play-badge.png" alt="Download on the Google Play Store" style="width: 250px; height: 100px;"></a>
                                <p>
                                    2段階認証でのログイン不能を防ぐため、以下のメールアドレスの有効性を確認します。<br>
                                    ボタンを押すとメールが送信され、有効性が確認できれば有効化されます。
                                </p>
                                    <div class="mb-3">
                                        <label for="EmailCheck" class="form-label">有効化メールアドレス</label>
                                        <input type="email" class="form-control" id="EmailCheck" style="text-align: center;" value="<?=$get['email']?>" disabled>
                                    </div>
                                    <button type="button" class="btn btn-primary" style="width: 40%;margin-top: 10px;" onclick="location.href='mypage.php'">キャンセル</button>
                                    <button type="button" class="btn btn-success" style="width: 40%;margin-top: 10px; margin-left: 10px;" onclick="location.href='TwoFactor/ActivateTwoFactor.php'">メールアドレスを確認</button>
                            <?php
                                // 2段階認証が設定されている場合
                                else:
                            ?>
                            <div style="text-align: center">
                            <p>お使いのアカウントは2段階認証が有効です。<br>
                                Google Authenticatorアプリを使用するため、お使いのスマートフォンにインストールして下さい。
                            </p>
                            <p>以下のQRコードをGoogle Authenticatorアプリで読み込んで下さい。</p>
                            <img src="<?=$qrCodeUrl?>">
                            <p>また、GoogleAuthenticatorにアクセスできなくなってしまった場合は、以下のメールアドレスにワンタイムパスワードを送信します。</p>
                            <input type="text" class="form-control" value="<?=$get['email']?>" style="text-align: center" disabled>
                            <p>
                                また、2段階認証を無効化する場合は以下のボタンを押します。
                            </p>
                            <button type="button" class="btn btn-danger" style="width: 90%;margin-top: 10px;" onclick="location.href='TwoFactor/Disable.php'">2段階認証の無効化</button>
                            <br>
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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
</body>
</html>