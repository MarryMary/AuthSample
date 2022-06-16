<?php
include dirname(__FILE__).'/../Tools/IsInGetTools.php';
include dirname(__FILE__).'/../Tools/MailSender.php';
include 'sql.php';
include 'UUID.php';

SessionStarter();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['email'])) {
        $type = 1;
        $mainstmt = $pdo->prepare("SELECT * FROM User WHERE email = :email");
        $mainstmt->bindParam(':email', $_POST['email'], PDO::PARAM_STR);
        $cr = $mainstmt->execute();
        $stmt = $pdo->prepare("SELECT * FROM PreUser WHERE email = :email");
        $stmt->bindParam(':email', $_POST['email'], PDO::PARAM_STR);
        $precr = $stmt->execute();
        if($cr && $precr){
            if(!is_bool($mainstmt->fetch()) && is_bool($stmt->fetch())){
                $stmt = $pdo->prepare("INSERT INTO PreUser (email, user_token, register_type) VALUES (:email, :user_token, :register_type)");
                $stmt->bindParam(':email', $_POST['email'], PDO::PARAM_STR);
                $stmt->bindParam(':user_token', $uuid, PDO::PARAM_STR);
                $stmt->bindParam(':register_type', $type, PDO::PARAM_INT);
                $res = $stmt->execute();
                if ($res) {
                    $template = file_get_contents(dirname(__FILE__).'/../Template/forget.html');
                    $template = str_replace('{{SERVICENAME}}', 'HolyLive', $template);
                    $template = str_replace('{{URL}}', 'http://localhost/AuthSample/MainPasswordReset.php?token='.$uuid, $template);
                    EmailSender($_POST['email'], 'パスワードリセットのご案内', $template);
                    $_SESSION['finished'] = True;
                    header('Location: /AuthSample/presend.php');
                } else {
                    $_SESSION['err'] = 'エラーが発生しました。もう一度お試し下さい。';
                    header('Location: /AuthSample/forget.php');
                }
                $pdo = null;
            }else{
                $_SESSION['err'] = 'そのメールアドレスは現在仮登録中か、登録されていない可能性があります。';
                header('Location: /AuthSample/forget.php');
            }
        }
    } else {
        $_SESSION['err'] = 'メールアドレスが入力されていません。';
        header('Location: /AuthSample/forget.php');
    }
} else {
    header('Location: /AuthSample/login.php');
}