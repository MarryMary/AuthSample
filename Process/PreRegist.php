<?php
include dirname(__FILE__).'/../Tools/IsInGetTools.php';
SessionStarter();
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['email'])) {
        include 'sql.php';
        include 'UUID.php';
        $type = 0;
        $prestmt = $pdo->prepare("SELECT * FROM User WHERE email = :email");
        $prestmt->bindParam(':email', $_POST['email'], PDO::PARAM_STR);
        $cr = $prestmt->execute();
        $stmt = $pdo->prepare("SELECT * FROM PreUser WHERE email = :email");
        $stmt->bindParam(':email', $_POST['email'], PDO::PARAM_STR);
        $precr = $stmt->execute();
        if($cr && $precr){
            if(is_bool($prestmt->fetch()) && is_bool($stmt->fetch())){
                $stmt = $pdo->prepare("INSERT INTO PreUser (email, user_token, register_type) VALUES (:email, :user_token, :register_type)");
                $stmt->bindParam(':email', $_POST['email'], PDO::PARAM_STR);
                $stmt->bindParam(':user_token', $uuid, PDO::PARAM_STR);
                $stmt->bindParam(':register_type', $type, PDO::PARAM_INT);
                $res = $stmt->execute();
                if ($res) {
                    include dirname(__FILE__).'/../Tools/MailSender.php';
                    $template = file_get_contents('mainregist.html');
                    $template = str_replace('{{SERVICENAME}}', 'HolyLive', $template);
                    $template = str_replace('{{URL}}', 'http://localhost/AuthSample/MainRegistration.php?token='.$uuid, $template);
                    EmailSender($_POST['email'], '本登録のご案内', $template);
                    $_SESSION['finished'] = True;
                    header('Location: /AuthSample/presend.php');
                } else {
                    $_SESSION['err'] = 'エラーが発生しました。もう一度お試し下さい。';
                    header('Location: /AuthSample/register_pre.php');
                }
                $pdo = null;
            }else{
                $_SESSION['err'] = 'そのメールアドレスは既に登録されています。';
                header('Location: /AuthSample/register_pre.php');
            }
        }
    } else {
        $_SESSION['err'] = 'メールアドレスが入力されていません。';
        header('Location: /AuthSample/register_pre.php');
    }
} else {
    header('Location: /AuthSample/login.php');
}