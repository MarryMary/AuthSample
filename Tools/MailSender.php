<?php
include dirname(__FILE__).'/../vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

function EmailSender(String $mailTo, String $title, String $Body)
{
    mb_language("japanese");
    mb_internal_encoding("UTF-8");

    $mail = new PHPMailer(true);

    $mail->CharSet = "iso-2022-jp";
    $mail->Encoding = "7bit";
    try{
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'marymarry0258@gmail.com';
        $mail->Password = 'picanyftxakyvgsp';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;
        $mail->setFrom('marymarry0258@gmail.com', mb_encode_mimeheader('HolyLive'));
        $mail->addAddress($mailTo);
        $mail->isHTML(true);
        $mail->Subject = mb_encode_mimeheader($title);
        $mail->Body = $Body;
        $mail->send();
        return True;
    }catch(\Exception $e){
        return False;
    }
}