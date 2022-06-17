<?php
/*
 * 2段階認証の設定完了画面に遷移するためのファイル
 */
// 必要ファイルのインクルード
include dirname(__FILE__).'/../vendor/autoload.php';
include dirname(__FILE__).'/../Tools/Session.php';
include dirname(__FILE__).'/../Tools/ValidateAndSecure.php';
include dirname(__FILE__).'/../Tools/SQL.php';
include dirname(__FILE__).'/../Template/ServiceData.php';

// セッション開始
SessionStarter();


// ログイン状態でない場合
if(!SessionIsIn('IsAuth') || SessionIsIn('IsAuth') && is_bool(SessionReader('IsAuth')) && !SessionReader('IsAuth')){
    header("location: /$SERVICE_ROOT/mypage.php");
}

// ユーザーテーブルをidから検索
$userid = SessionReader('UserId');
$stmt = $pdo->prepare("SELECT * FROM User WHERE id = :id");
$stmt->bindValue(":id", $userid, PDO::PARAM_INT);
$res = $stmt->execute();

// SQLが正しく実行できなかった場合
if(!$res){
    header("Location: /$SERVICE_ROOT/Process/Logout.php");
}else{
    $data = $stmt->fetch();
    if(is_bool($data)){
        header("Location: /$SERVICE_ROOT/Process/Logout.php");
    }else{
        $ga = new PHPGangsta_GoogleAuthenticator();
        $secret = $data["TwoFactorSecret"];
        $code = filter_input(INPUT_POST, 'token');
        $discrepancy = 2;
        $checkResult = $ga->verifyCode($secret, $code, $discrepancy);
        if($checkResult){
            $stmt = $pdo->prepare("DELETE FROM PreUser WHERE user_token = :token");
            $stmt->bindValue(":token", $_SESSION["token"], PDO::PARAM_STR);
            $result = $stmt->execute();
            $title = 'Google Authenticator Enabled';
            $card_name = '設定完了';
            $message = '全ての設定が完了しました！';
            $errtype = False;
            if(array_key_exists('err', $_SESSION)){
                $errtype = True;
                $message = $_SESSION['err'];
                unset($_SESSION['err']);
            }

            $GAuthJS = '';

            $form = <<<EOF
<p>
    全ての設定が完了しました。<br>
    次回認証時から2段階認証が有効化されます。<br>
    <button type="button" class="btn btn-primary" onclick="location.href='/AuthSample/mypage.php'" style="width: 90%;">ホームへ</button>
</p>
EOF;

            $GAuthButton = '';
            $option = '';

            $scriptTo = 'JavaScript/Login.js';
            $JS = '<script src="https://unpkg.com/jwt-decode/build/jwt-decode.js"></script>';

            include dirname(__FILE__).'/../Template/BaseTemplate.php';
        }else{
            $_SESSION["err"] = "コードが異なります。";
            header("Location: /AuthSample/TwoFactor/EnableTwoFactor.php?token=".$_SESSION["token"]);
        }
    }
}