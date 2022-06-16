<?php
require dirname(__FILE__).'/../vendor/autoload.php';
require dirname(__FILE__).'/../Tools/IsInGetTools.php';
include dirname(__FILE__).'/../Tools/ValidateAndSecure.php';
require dirname(__FILE__).'/../Process/sql.php';

SessionStarter();
if(!isset($_SESSION["IsAuth"]) || isset($_SESSION["IsAuth"]) && is_bool($_SESSION["IsAuth"]) && !$_SESSION["IsAuth"]){
    header("location: /AuthSample/mypage.php");
}

$stmt = $pdo->prepare("SELECT * FROM User WHERE id = :id");
$stmt->bindValue(":id", $_SESSION["UserId"], PDO::PARAM_INT);
$res = $stmt->execute();
if(!$res){
    header("Location: /AuthSample/Process/Logout.php");
}else{
    $data = $stmt->fetch();
    if(is_bool($data)){
        header("Location: /AuthSample/Process/Logout.php");
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