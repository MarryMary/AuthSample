<?php
include dirname(__FILE__).'/../Tools/IsInGetTools.php';
SessionStarter();
if($_SERVER['REQUEST_METHOD'] == 'POST'){
    if(isset($_SESSION['filename']) && isset($_SESSION['username']) && isset($_SESSION['password']) && isset($_SESSION['email'])){
        include 'sql.php';
        $flag = 0;
        $stmt = $pdo->prepare("INSERT INTO User (email, pass, user_name,user_pict, delete_flag) VALUES (:email, :pass, :user_name,:user_pict, :delete_flag)");
        $password = password_hash($_SESSION['password'], PASSWORD_DEFAULT);
        $stmt->bindParam( ':email', $_SESSION['email'], PDO::PARAM_STR);
        $stmt->bindParam( ':pass', $password, PDO::PARAM_STR);
        $stmt->bindParam( ':user_name', $_SESSION['username'], PDO::PARAM_STR);
        $stmt->bindParam( ':user_pict', $_SESSION['filename'], PDO::PARAM_STR);
        $stmt->bindParam( ':delete_flag', $flag, PDO::PARAM_INT);
        $res = $stmt->execute();
        if($res){
            $stmt = $pdo->prepare("DELETE FROM PreUser WHERE user_token = :user_token");
            $stmt->bindParam( ':user_token', $_SESSION['token'], PDO::PARAM_STR);
            $res = $stmt->execute();
            $_SESSION['registration'] = True;
            header('Location: /AuthSample/regfinish.php');
        }else{
            header('Location: /AuthSample/login.php');
        }
    }else{
        header('Location: /AuthSample/login.php');
    }
}else{
    header('Location: /AuthSample/login.php');
}