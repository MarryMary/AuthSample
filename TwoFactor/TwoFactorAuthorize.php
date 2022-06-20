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
    header('Location: /'.$SERVICE_ROOT.'/Auth/login.php');
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
    header("Location: /'.$SERVICE_ROOT.'/MyPage/home.php");
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

// セッション開始
SessionStarter();

// もしもログインしていないか2段階認証未実施の場合はログイン画面に遷移
if(!SessionIsIn('IsAuth') || is_bool(SessionReader('IsAuth')) && !SessionReader('IsAuth')){
    header('Location: /'.$SERVICE_ROOT.'/Auth/login.php');
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
    header("Location: /$SERVICE_ROOT/MyPage/profilesetting.php");
}

$name = $get['user_name'];
$pict = $get['user_pict'];

if($get['IsTwoFactor'] == 0){

    $paste = <<<EOF
    <p>
    お使いのアカウントは2段階認証を設定可能です。<br>
    2段階認証の利用にはGoogle Authenticatorアプリが必要です。<br>
    以下からダウンロードして下さい。
</p>
<a href="https://apps.apple.com/us/app/google-authenticator/id388497605?itsct=apps_box_badge&amp;itscg=30200" style="display: inline-block; overflow: hidden; border-radius: 13px; width: 250px; height: 83px;"><img src="https://tools.applemediaservices.com/api/badges/download-on-the-app-store/black/en-us?size=250x83&amp;releaseDate=1284940800&h=7fc6b39acc8ae5a42ad4b87ff8c7f88d" alt="Download on the App Store" style="border-radius: 13px; width: 230px; height: 83px;"></a>
<a href="https://play.google.com/store/apps/details?id=com.google.android.apps.authenticator2" style="display: inline-block; overflow: hidden; border-radius: 13px; width: 250px; height: 83px;"><img src="/{$SERVICE_ROOT}/Resources/google-play-badge.png" alt="Download on the Google Play Store" style="width: 250px; height: 100px;"></a>
<p>
    2段階認証でのログイン不能を防ぐため、以下のメールアドレスの有効性を確認します。<br>
    ボタンを押すとメールが送信され、有効性が確認できれば有効化されます。
</p>
    <div class="mb-3">
        <label for="EmailCheck" class="form-label">有効化メールアドレス</label>
        <input type="email" class="form-control" id="EmailCheck" style="text-align: center;" value="{$get['email']}" disabled>
    </div>
    <button type="button" class="btn btn-primary" style="width: 40%;margin-top: 10px;" onclick="location.href='/{$SERVICE_ROOT}/MyPage/profilesetting.php'">キャンセル</button>
    <button type="button" class="btn btn-success" style="width: 40%;margin-top: 10px; margin-left: 10px;" onclick="location.href='ActivateTwoFactor.php'">メールアドレスを確認</button>
EOF;

}else{
    $paste = <<<EOF
<div style="text-align: center">
<p>お使いのアカウントは2段階認証が有効です。<br>
    Google Authenticatorアプリを使用するため、お使いのスマートフォンにインストールして下さい。
</p>
<p>以下のQRコードをGoogle Authenticatorアプリで読み込んで下さい。</p>
<img src="<?=$qrCodeUrl?>">
<p>また、GoogleAuthenticatorにアクセスできなくなってしまった場合は、以下のメールアドレスにワンタイムパスワードを送信します。</p>
<input type="text" class="form-control" value="{$get['email']}" style="text-align: center" disabled>
<p>
    また、2段階認証を無効化する場合は以下のボタンを押します。
</p>
<button type="button" class="btn btn-danger" style="width: 90%;margin-top: 10px;" onclick="location.href='Disable.php'">2段階認証の無効化</button>
<br>
</div>
EOF;
}

$content = <<<EOF
<div class="box-centering bootstrap">
    <div class="content-card" style="margin-top: 30px;">
        <div class="menu">
        <button type="button" class="btn btn-primary" style="width: 40%;margin-top: 10px;" onclick="location.href='/{$SERVICE_ROOT}/MyPage/profilesetting.php'">＜＜ホームに戻る</button>
        <br>
        <div style="text-align: center">
            <h1>2段階認証の追加</h1>
            <p>お使いのアカウントに2段階認証を追加します。</p>
            <hr>
            {$paste}
        </div>
    </div>
</div>
EOF;

include 'Template/dashboard.php';
?>