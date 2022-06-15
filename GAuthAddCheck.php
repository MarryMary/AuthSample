<?php
include dirname(__FILE__).'/Tools/IsInGetTools.php';
include dirname(__FILE__).'/Tools/ValidateAndSecure.php';

SessionStarter();
if(isset($_SESSION['email']) && isset($_SESSION['userid'])){
    $title = 'Registration';
    $card_name = '新規登録';
    $message = '以下の内容で登録します。';
    $errtype = False;
    if(array_key_exists('err', $_SESSION)){
        $errtype = True;
        $message = $_SESSION['err'];
        unset($_SESSION['err']);
    }

    $file = $_SESSION['filename'];

    $email = $_SESSION['email'];
    $username = $_SESSION['username'];
    $password = str_repeat("●", strlen($_SESSION['password']));

    $GAuthJS = <<<EOF
    <link href="//cdnjs.cloudflare.com/ajax/libs/cropper/3.1.6/cropper.min.css" rel="stylesheet">
    <script src="//cdnjs.cloudflare.com/ajax/libs/cropper/3.1.6/cropper.min.js"></script>
    EOF;

    $form = <<<EOF
    <form action="Process/GAuthAdd.php" method="POST" enctype="multipart/form-data">
        <input type='email' name='email' class="form-control" placeholder='メールアドレス' style='margin-bottom: 3%;' value='{$email}' disabled>
        <input type='text' name='username' class="form-control" placeholder='ユーザー名' style='margin-bottom: 3%;' value='{$username}' disabled>
        <input type='password' name='password' class="form-control" placeholder='パスワード' style='margin-bottom: 3%;' value='{$password}'>
        <p>プロフィール画像</p>
        <div style="text-align: center">
            <img src='{$file}' alt='profile' width='200' height='200'>
        </div>
        <div style="text-align: center; margin-top: 10px;">
            <button type="button" class="btn btn-primary" onclick="history.back()" style="width: 40%;">戻る</button>
            <button type="submit" class="btn btn-success" style="width: 40%;">登録</button><br>
        </div>
    </form>

    EOF;

    $GAuthButton = '';

    $option = '';


    $scriptTo = 'JavaScript/Register.js';
    $JS = '';

    include dirname(__FILE__).'/Template/BaseTemplate.php';
}else{
    header('Location: /AuthSample/login.php');
}