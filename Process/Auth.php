<?php
if($_SERVER['REQUEST_METHOD'] == 'POST'){
    if(isset($_POST['email']) && isset($_POST['password'])){
        include 'sql.php';
        $stmt = $pdo->prepare("SELECT * FROM User WHERE email = :email");
        $stmt->bindParam( ':email', $_POST['email'], PDO::PARAM_STR);
        $res = $stmt->execute();
        if($res) {
            $data = $stmt->fetch();
            $password = $data['password'];
            if(!is_bool($data) && password_verify($_POST['password'], $password)){
                $_SESSION['IsAuth'] = True;
                $_SESSION['UserId'] = $data['id'];
                header('Location: /AuthSample/mypage.php');
            }else{
                $_SESSION['err'] = 'メールアドレスまたはパスワードが間違っています。';
            }
        }else{
            $_SESSION['err'] = 'エラーが発生しました。もう一度お試し下さい。';
        }
        $pdo = null;
    }else{
        $_SESSION['err'] = 'メールアドレスまたはパスワードが入力されていません。';
    }
}else{
    header('Location: /AuthSample/login.php');
}