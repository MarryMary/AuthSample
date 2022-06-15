<?php
include dirname(__FILE__).'/../Tools/IsInGetTools.php';
if(!isset($_SESSION["IsAuth"]) || isset($_SESSION["IsAuth"]) && is_bool($_SESSION["IsAuth"]) && $_SESSION["IsAuth"] && !isset($_SESSION["NeedTwoFactor"])){
    header("location: /AuthSample/login.php");
}

include dirname(__FILE__).'/../Tools/MailSender.php';
$str = 'abcdefghijklmnopqrstuvwxyz0123456789';
$rand_str = substr(str_shuffle($str), 0, 8);
$template = file_get_contents('CodeTemplate.html');
$template = str_replace('{{TOKEN}}', $rand_str, $template);
EmailSender();