<?php
include dirname(__FILE__).'/../Tools/IsInGetTools.php';
SessionStarter();
if($_SERVER['REQUEST_METHOD'] == 'POST'){
    if(isset($_SESSION['email']) && isset($_POST['username'])&& isset($_POST['password1']) && isset($_POST['password2'])){
        include 'Validate.php';
        if($_POST['password1'] == $_POST['password2']){
            if(EmailValid($_SESSION['email']) && PasswordValid($_POST['password1']) && UserNameValid($_POST['username'])){
                $_SESSION['password'] = $_POST['password1'];
                $_SESSION['username'] = $_POST['username'];
                include dirname(__FILE__).'/../Tools/Uploader.php';
                if(isset($file)){
                    $_SESSION['filename'] = $file;
                }else{
                    $_SESSION['err'] = "ファイルのアップロードに失敗しました。";
                    header('Location: /AuthSample/MainRegistration.php?token='.$_SESSION['token']);
                }
                header('Location: /AuthSample/RegCheck.php');
            }else{
                $_SESSION['err'] = 'メールアドレスまたはパスワード、ユーザー名のいずれかが条件に一致しません。';
                header('Location: /AuthSample/MainRegistration.php?token='.$_SESSION['token']);
            }
        }else{
            $_SESSION['err'] = 'パスワードが一致しません。';
            header('Location: /AuthSample/MainRegistration.php?token='.$_SESSION['token']);
        }
    }else{
        $_SESSION['err'] = 'メールアドレスまたはパスワードが入力されていません。';
        header('Location: /AuthSample/MainRegistration.php?token='.$_SESSION['token']);
    }
}else{
    header('Location: /AuthSample/login.php');
}