<?php
/*
 * メールを送信する関数を保有するファイルです。
 */
// 必要ファイルのインクルードとuse
include dirname(__FILE__).'/../vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// メール送信関数
/*
 * $mailTo = メール送信先（ユーザーのメールアドレス）
 * $title = メールのタイトル
 * $Body = メールの本文(HTML)
 */
function EmailSender(String $mailTo, String $title, String $Body): bool
{
    // 日本語対応
    mb_language("japanese");
    mb_internal_encoding("UTF-8");

    // PHPMailer(メール送信ライブラリ)をインスタンス化
    $mail = new PHPMailer(true);

    // メールのエンコーディング設定
    $mail->CharSet = "iso-2022-jp";
    $mail->Encoding = "7bit";
    // 送信試行
    try{
        // SMTPを有効化し、メールアドレスとパスワードを設定（Gmailのもの）
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'marymarry0258@gmail.com';
        $mail->Password = 'picanyftxakyvgsp';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;
        $mail->setFrom('marymarry0258@gmail.com', mb_encode_mimeheader('HolyLive'));

        // 指定した先に本文をHTMLとして、タイトルもバインドして送信
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