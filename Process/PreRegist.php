<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['email'])) {
        include 'sql.php';
        include 'UUID.php';
        $type = 0;
        $stmt = $pdo->prepare("INSERT INTO PreUser (email, user_token, register_type) VALUES (:email, :user_token, :register_type)");
        $stmt->bindParam(':email', $_POST['email'], PDO::PARAM_STR);
        $stmt->bindParam(':user_token', $uuid, PDO::PARAM_STR);
        $stmt->bindParam(':register_type', $type, PDO::PARAM_INT);
        $res = $stmt->execute();
        if ($res) {
            include dirname(__FILE__).'/../IsInGetTools.php';
            SessionStarter();
            $_SESSION['finished'] = True;
            header('Location: /AuthSample/presend.php');
        } else {
            $_SESSION['err'] = 'エラーが発生しました。もう一度お試し下さい。';
        }
        $pdo = null;
    } else {
        $_SESSION['err'] = 'メールアドレスまたはパスワードが入力されていません。';
    }
} else {
    header('Location: /AuthSample/login.php');
}