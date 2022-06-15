<?php
include dirname(__FILE__).'/Tools/IsInGetTools.php';
include dirname(__FILE__).'/Tools/ValidateAndSecure.php';

SessionStarter();
if(isset($_SESSION['email']) && isset($_SESSION['userid']) && isset($_SESSION['username'])){
    $title = 'Registration';
    $card_name = '新規登録';
    $message = 'ログインを行う前に以下の情報を追加して下さい。';
    $errtype = False;
    if(isset($_SESSION["err"])){
        $errtype = True;
        $message = $_SESSION['err'];
        unset($_SESSION['err']);
    }

    $email = $_SESSION['email'];
    $username = $_SESSION['username'];

    $GAuthJS = <<<EOF
<link href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.12/cropper.css" rel="stylesheet" type="text/css">
EOF;

    $form = <<<EOF
    <form action="Process/GCheck.php" method="POST" enctype="multipart/form-data">
        <input type='email' name='email' class="form-control" placeholder='メールアドレス' style='margin-bottom: 3%;' value='{$email}' disabled>
        <input type='text' name='username' class="form-control" placeholder='ユーザー名' style='margin-bottom: 3%;' value='{$username}' disabled>
        <input type='password' name='password1' class="form-control" placeholder='パスワード' style='margin-bottom: 3%;'>
        <input type='password' name='password2' class="form-control" placeholder='パスワード(確認用)' style='margin-bottom: 3%;'>
        <p>プロフィール画像</p>
        <input type="file" name="UserPict" id="UserImage">
        <img id="selectImage" style="max-width:500px;">
        <input type="hidden" id="imageX" name="UserImageX" value="0"/>
        <input type="hidden" id="imageY" name="UserImageY" value="0"/>
        <input type="hidden" id="imageW" name="UserImageW" value="0"/>
        <input type="hidden" id="imageH" name="UserImageH" value="0"/>
        <div style="text-align: center; margin-top: 10px;">
            <button type='submit' class='btn btn-primary' style="width: 80%;">次へ</button>
        </div>
    </form>

    EOF;

    $GAuthButton = '';

    $option = '';


    $scriptTo = 'JavaScript/Register.js';
    $JS = '<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.12/cropper.js" type="text/javascript"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-cropper/1.0.1/jquery-cropper.js" type="text/javascript"></script>';

    include dirname(__FILE__).'/Template/BaseTemplate.php';
}else{
    var_dump(isset($_SESSION['email']));
    exit;
    header('Location: /AuthSample/login.php');
}