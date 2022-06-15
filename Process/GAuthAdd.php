<?php
include dirname(__FILE__).'/../Tools/IsInGetTools.php';
SessionStarter();
if($_SERVER['REQUEST_METHOD'] == 'POST'){
    if(isset($_SESSION['filename']) && isset($_SESSION['username']) && isset($_SESSION['password']) && isset($_SESSION['userid']) && isset($_SESSION['email'])){
        include 'sql.php';
        $flag = 0;
        $stmt = $pdo->prepare("INSERT INTO User (email, pass, user_name,user_pict, GAuthID, delete_flag) VALUES (:email, :pass, :user_name,:user_pict, :gauth_id, :delete_flag)");
        $stmt->bindParam( ':email', $_SESSION['email'], PDO::PARAM_STR);
        $stmt->bindParam( ':user_name', $_SESSION['username'], PDO::PARAM_STR);
        $stmt->bindParam( ':pass', password_hash($_SESSION['password'], PASSWORD_DEFAULT), PDO::PARAM_STR);
        $stmt->bindParam( ':user_pict', $_SESSION['filename'], PDO::PARAM_STR);
        $stmt->bindParam( ':gauth_id', $_SESSION['userid'], PDO::PARAM_STR);
        $stmt->bindParam( ':delete_flag', $flag, PDO::PARAM_INT);
        $res = $stmt->execute();
        if($res){
            $_SESSION['registration'] = True;
            header('Location: /AuthSample/regfinish.php');
        }else{
            echo "a";
            exit;
            header('Location: /AuthSample/login.php');
        }
    }else{
        header('Location: /AuthSample/login.php');
    }
}else{
    echo "c";
    exit;
    header('Location: /AuthSample/login.php');
}