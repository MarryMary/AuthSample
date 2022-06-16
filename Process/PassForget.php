<?php
include dirname(__FILE__).'/../Tools/IsInGetTools.php';
include 'sql.php';
include 'Validate.php';

SessionStarter();

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    if(isset($_POST['password1']) && isset($_POST['password2'])){
        if($_POST['password1'] == $_POST['password2']){
            if(PasswordValid($_POST['password1'])){
                $password = password_hash($_POST['password1'], PASSWORD_DEFAULT);

                $stmt = $pdo->prepare("SELECT * FROM PreUser WHERE user_token = :token");
                $stmt->bindParam(':token', $_SESSION['token'], PDO::PARAM_STR);
                $stmt->execute();
                $data = $stmt->fetch();

                $stmt = $pdo->prepare("SELECT * FROM User WHERE email = :email");
                $stmt->bindParam(':email', $data['email'], PDO::PARAM_STR);
                $stmt->execute();
                $get = $stmt->fetch();

                if(!password_verify($_POST["password1"], $get['pass'])) {
                    $flag = 0;
                    $stmt = $pdo->prepare("UPDATE User SET pass = :password WHERE id = :id");
                    $stmt->bindParam(':password', $password, PDO::PARAM_STR);
                    $stmt->bindParam(':id', $data['id'], PDO::PARAM_STR);
                    $res = $stmt->execute();
                    if ($res) {
                        $stmt = $pdo->prepare("DELETE FROM PreUser WHERE user_token = :user_token");
                        $stmt->bindParam(':user_token', $_SESSION['token'], PDO::PARAM_STR);
                        $res = $stmt->execute();
                        $_SESSION['registration'] = True;
                        header('Location: /AuthSample/ResetFinish.php');
                    } else {
                        header('Location: /AuthSample/login.php');
                    }
                }else{
                    $_SESSION['err'] = '現在のパスワードと同じパスワードを設定することはできません。';
                    header('Location: /AuthSample/MainPasswordReset.php?token='.$_SESSION['token']);
                }
            }else{
                $_SESSION['err'] = 'パスワードが条件に一致しません。';
                header('Location: /AuthSample/MainPasswordReset.php?token='.$_SESSION['token']);
            }
        }else{
            $_SESSION['err'] = 'パスワードが一致しません。';
            header('Location: /AuthSample/MainPasswordReset.php?token='.$_SESSION['token']);
        }
    }else{
        $_SESSION['err'] = 'メールアドレスまたはパスワードが入力されていません。';
        header('Location: /AuthSample/MainPasswordReset.php?token='.$_SESSION['token']);
    }
}else{
    header('Location: /AuthSample/login.php');
}