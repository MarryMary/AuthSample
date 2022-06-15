<?php
include dirname(__FILE__).'/../Tools/IsInGetTools.php';
SessionStarter();
if($_SERVER['REQUEST_METHOD'] == 'POST'){
    if(isset($_SESSION['filename']) && isset($_SESSION['UserName']) && isset($_SESSION['password']) && isset($_SESSION['userid']) && isset($_SESSION['email'])){
        include 'sql.php';
        $stmt = $pdo->prepare("INSERT INTO User");
        $stmt->bindParam( ':email', $payload['email'], PDO::PARAM_STR);
        $res = $stmt->execute();
    }
}else{
    header('Location: /AuthSample/login.php');
}