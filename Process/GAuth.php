<?php

use Google\Service\Fitness\Session;

require_once dirname(__FILE__).'/../vendor/autoload.php';
include dirname(__FILE__).'/../Tools/IsInGetTools.php';

SessionStarter();
if($_SERVER['REQUEST_METHOD'] == 'POST'){
    if(isset($_POST['id_token'])){
        $id_token = filter_input(INPUT_POST, 'id_token');
        define('CLIENT_ID', '345840626602-q37bp5di0lrr53n3bar423uhg90rff67.apps.googleusercontent.com');
        $client = new Google_Client(['client_id' => CLIENT_ID]); 
        $payload = $client->verifyIdToken($id_token);
        if ($payload) {
            $userid = $payload['sub'];
            include 'sql.php';
            $stmt = $pdo->prepare("SELECT * FROM User WHERE GAuthID = :id");
            $stmt->bindParam( ':id', $userid, PDO::PARAM_STR);
            $res = $stmt->execute();
            if($res){
                $data = $stmt->fetch();
                if(is_bool($data)){
                    $stmt = $pdo->prepare("SELECT * FROM User WHERE email = :email");
                    $stmt->bindParam( ':email', $payload['email'], PDO::PARAM_STR);
                    $res = $stmt->execute();
                    if($res){
                        $data = $stmt->fetch();
                        if(!is_bool($data)){
                            $userid = $payload['sub'];
                            $stmt = $pdo->prepare("UPDATE User SET GAuthID = :id Where email = :email");
                            $stmt->bindParam( ':email', $payload['email'], PDO::PARAM_STR);
                            $stmt->bindParam( ':id', $userid, PDO::PARAM_STR);
                            $res = $stmt->execute();
                            $_SESSION['IsAuth'] = True;
                            $_SESSION['UserId'] = $data['id'];
                            header('Location: /AuthSample/mypage.php');
                        }else{
                            $_SESSION['email'] = $payload['email'];
                            $_SESSION['userid'] = $payload['sub'];
                            $_SESSION['username'] = $payload['name'];
                            header('Location: /AuthSample/GAuthAdd.php');
                        }
                    }
                }else{
                    $_SESSION['IsAuth'] = True;
                    $_SESSION['UserId'] = $data['id'];
                    header('Location: /AuthSample/mypage.php');
                }
            }else{
                $_SESSION['err'] = 'エラーが発生しました。もう一度お試し下さい。';
                header('Location: /AuthSample/login.php');
            }
            $pdo = null;
        }else{
            $_SESSION['err'] = 'エラーが発生しました。もう一度お試し下さい。';
            header('Location: /AuthSample/login.php');
        }
    }else{
        header('Location: /AuthSample/login.php');
    }
}else{
    header('Location: /AuthSample/login.php');
}