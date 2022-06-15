<?php
include dirname(__FILE__).'/../Tools/IsInGetTools.php';
require_once "PhpGangsta/GoogleAuthenticator.php";
 
SessionStarter();

if(isset($_SESSION["IsAuth"]) && is_bool($_SESSION["IsAuth"]) && !$_SESSION["IsAuth"] && isset($_SESSION["NeedTwoFactor"])){

    $ga = new PHPGangsta_GoogleAuthenticator();
    $secret = $_SESSION["secret"];
    $oneCode = filter_input(INPUT_POST, 'oneCode');

    $discrepancy = 2;

    $checkResult = $ga->verifyCode($secret, $oneCode, $discrepancy);
    if($checkResult){
        $_SESSION["IsAuth"] = True;
        header("Location: /AuthSample/mypage.php");
    }
}else{
    header("Location: /AuthSample/login.php");
}