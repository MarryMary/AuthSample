<?php
include dirname(__FILE__).'/../Tools/IsInGetTools.php';

if($_SERVER["REQUEST_METHOD"] === "POST"){
    SessionStarter();
    if(isset($_SESSION["2Factor-Token"]) && isset($_POST["token"]) && trim($_SESSION["2Factor-Token"]) == trim($POST["token"])){
        $_SESSION["IsAuth"] = True;
        unset($_SESSION["2Factor-Token"]);
        header("Location: /SampleAuth/mypage.php");
    }else{
        $_SESSION["err"] = "コードが異なります。";
        header("Location: /SampleAuth/TwoFactor/MailFactorSend.php");
    }
}else{
    header("Location: /SampleAuth/login.php");
}