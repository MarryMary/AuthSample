<?php
include dirname(__FILE__).'/../Tools/IsInGetTools.php';

SessionStarter();
if($_SERVER['REQUEST_METHOD'] == 'POST'){
    if(isset($_POST['email']) && isset($_POST['password'])){
        include 'sql.php';
        $stmt = $pdo->prepare("SELECT * FROM User WHERE email = :email");
        $stmt->bindParam( ':email', $_POST['email'], PDO::PARAM_STR);
        $res = $stmt->execute();
        if($res) {
            $data = $stmt->fetch();
            $password = $data['pass'];
            if(!is_bool($data) && password_verify($_POST['password'], $password)){
                if($data['IsTwoFactor'] == 1){
                    $_SESSION['IsAuth'] = False;
                    $_SESSION['UserId'] = $data['id'];
                    $_SESSION['NeedTwoFactor'] = True;
                    header('Location: /AuthSample/TwoFactor/whichTwoFactor.php');
                }else{
                    $_SESSION['IsAuth'] = True;
                    $_SESSION['UserId'] = $data['id'];
                    header('Location: /AuthSample/mypage.php');
                }
            }else{
                $_SESSION['err'] = 'メールアドレスまたはパスワードが間違っています。';
                header('Location: /AuthSample/login.php');
            }
        }else{
            $_SESSION['err'] = 'エラーが発生しました。もう一度お試し下さい。';
            header('Location: /AuthSample/login.php');
        }
        $pdo = null;
    }else{
        $_SESSION['err'] = 'メールアドレスまたはパスワードが入力されていません。';
        header('Location: /AuthSample/login.php');
    }
}else{
    header('Location: /AuthSample/login.php');
}