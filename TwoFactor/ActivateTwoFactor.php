<?php
include dirname(__FILE__).'/../Tools/IsInGetTools.php';
include dirname(__FILE__).'/../Tools/MailSender.php';
include dirname(__FILE__).'/../Process/UUID.php';
include dirname(__FILE__).'/../Process/sql.php';

SessionStarter();

if(isset($_SESSION["IsAuth"]) && is_bool($_SESSION["IsAuth"]) && $_SESSION["IsAuth"]){
    $stmt = $pdo->prepare("SELECT * FROM User WHERE id = :id");
    $stmt->bindValue(':id',$_SESSION["UserId"], PDO::PARAM_INT);
    $res = $stmt->execute();
    if($res){
        $data = $stmt->fetch();
        if(!is_bool($data)){
            if($data['IsTwoFactor'] == 0){
                $url = 'http://localhost/AuthSample/TwoFactor/EnableTwoFactor.php?token='.$uuid;
                $template = file_get_contents(dirname(__FILE__).'/../Template/TwoFactorEnable.html');
                $template = str_replace('{{URL}}', $url, $template);
                EmailSender($data['email'], '2段階認証有効化のご案内', $template);
                $stmt = $pdo->prepare("INSERT INTO PreUser (user_token, email, register_type) VALUES (:token, :email, :type)");
                $stmt->bindValue(':token', $uuid, PDO::PARAM_STR);
                $stmt->bindValue(':email', $data['email'], PDO::PARAM_STR);
                $stmt->bindValue(':type', 3, PDO::PARAM_INT);
                $stmt->execute();
                $_SESSION['finished'] = True;
                $_SESSION['twofactor'] = True;
                header("Location: /AuthSample/presend.php");
            }else{
                header("Location: /AuthSample/mypage.php");
            }
        }else{
            header("Location: /AuthSample/login.php");
        }
    }else{
        //$_SESSION["err"] = "問題が発生しました。";
        header("Location: /AuthSample/TwoFactorAuthorize.php");
    }
}else{
    header("Location: /AuthSample/login.php");
}