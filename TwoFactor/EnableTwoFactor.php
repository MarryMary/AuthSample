<?php
/*
 * 2段階認証を有効化するための処理ファイルです
 */
// 必要ファイルのインクルード
include dirname(__FILE__).'/../Tools/Session.php';
include dirname(__FILE__).'/../vendor/autoload.php';
include dirname(__FILE__).'/../Tools/ValidateAndSecure.php';
include dirname(__FILE__).'/../Tools/MailSender.php';
include dirname(__FILE__).'/../Tools/SQL.php';
include dirname(__FILE__).'/../Template/ServiceData.php';

//セッション開始
SessionStarter();

// トークンがGET送信されていない場合
if(!isset($_GET["token"])){
    header("location: /$SERVICE_ROOT/mypage.php");
}

// 仮ユーザーテーブルからトークンを基にデータを検索
$stmt = $pdo->prepare("SELECT * FROM PreUser WHERE user_token = :token");
$stmt->bindValue(":token", $_GET["token"], PDO::PARAM_STR);
$res = $stmt->execute();

// 秘密鍵を直接表示するか
$show_code = False;
// もしcant_readフラグがGET送信されている場合は秘密鍵を直接表示する
if(isset($_GET["cant_read"])){
    $show_code = True;
}

// もしSQLが正しく実行できなかった場合
if(!$res){
    header("Location: /AuthSample/Process/Logout.php");
// もしSQLが正しく実行できた場合
}else{
    // データを取得
    $data = $stmt->fetch();

    // データが存在しなかった場合
    if(is_bool($data)){
        header("Location: /$SERVICE_ROOT/Process/Logout.php");
    // データが存在した場合
    }else{
        // Google Authenticatorライブラリをインスタンス化
        $ga = new PHPGangsta_GoogleAuthenticator();
        // 秘密鍵を生成
        $secret = $ga->createSecret();
        SessionInsert('token', $_GET['token']);

        $userid = SessionReader('UserId');
        // ユーザーテーブルを更新して2段階認証を有効化、秘密鍵をインサート
        $stmt = $pdo->prepare("UPDATE User SET TwoFactorSecret = :secret, IsTwoFactor = 1 WHERE id = :id");
        $stmt->bindValue(":secret", $secret, PDO::PARAM_STR);
        $stmt->bindValue(":id", $userid, PDO::PARAM_STR);
        $result = $stmt->execute();

        // ユーザーテーブルを検索
        $stmt = $pdo->prepare("SELECT * FROM User WHERE id = :id");
        $stmt->bindValue(":id", $userid, PDO::PARAM_STR);
        $result = $stmt->execute();

        // SQLが正しく実行できた場合
        if($result){
            // データを取得
            $get = $stmt->fetch();
        // SQLが正しく実行できなかった場合
        }else{
            $get = False;
        }


        $title = 'Two-Factor Authorize Enabled';
        $card_name = '2段階認証の有効化';
        $message = 'Google Authenticatorアプリを有効化する';

        // 2段階認証用コードを生成
        $qrCodeUrl = $ga->getQRCodeGoogleUrl($get['user_name'], $secret, 'HolyLive');
        $errtype = False;
        $token = trim($_GET["token"]);
        if(SessionIsIn('token')){
            $errtype = True;
            $message = SessionReader('err');
            SessionUnset('err');
        }

        // データが取得できなかった場合のテンプレート
        if(is_bool($get)){
            $form = <<<EOF
<p style="color: red;">
    問題が発生しました。
</p>
<div style="text-align: center;">
    <button type="button" class="btn btn-primary" onclick="location.href='/AuthSample/TwoFactorAuthorize.php'" style="width: 90%;">設定画面へ</button>
</div>
EOF;
        // 秘密鍵を直接表示する場合のテンプレート
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
<button type='submit' class='btn btn-success' style="width: 40%;">送信</button>
</div>
</form>
EOF;
        // 秘密鍵のQRコードを表示するテンプレート
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


        $scriptTo = 'JavaScript/Login.js';
        $JS = '<script src="https://unpkg.com/jwt-decode/build/jwt-decode.js"></script>';

        include dirname(__FILE__).'/../Template/BaseTemplate.php';
    }
}