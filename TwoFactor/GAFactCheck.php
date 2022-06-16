<?php
require dirname(__FILE__).'/../vendor/autoload.php';
require dirname(__FILE__).'/../Tools/IsInGetTools.php';
require dirname(__FILE__).'/../Process/sql.php';

SessionStarter();
if(!isset($_SESSION["IsAuth"]) || !isset($_SESSION["NeedTwoFactor"]) || isset($_SESSION["IsAuth"]) && is_bool($_SESSION["IsAuth"]) && $_SESSION["IsAuth"]){
    header("location: /AuthSample/mypage.php");
}

$stmt = $pdo->prepare("SELECT * FROM User WHERE id = :id");
$stmt->bindValue(":id", $_SESSION["UserId"], PDO::PARAM_INT);
$res = $stmt->execute();
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
            $_SESSION["IsAuth"] = True;
            unset($_SESSION["2Factor-Token"]);
            header("Location: /AuthSample/mypage.php");
        }else{
            $_SESSION["err"] = "コードが異なります。";
            header("Location: /AuthSample/TwoFactor/GoogleAuthenticator.php");
        }
    }
}