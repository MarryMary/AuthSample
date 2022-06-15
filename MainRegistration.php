<?php
include 'Tools/IsInGetTools.php';
include 'Tools/ValidateAndSecure.php';
SessionStarter();

if(isset($_GET["token"])){
    include 'Process/sql.php';
    $stmt = $pdo->prepare("delete from PreUser WHERE register_at<=sysdate() - interval 1 day");
    $stmt->execute();
    $stmt = $pdo->prepare("SELECT * FROM PreUser WHERE user_token = :token");
    $stmt->bindValue(':token', $_GET["token"], PDO::PARAM_STR);
    $res = $stmt->execute();
    if($res){
        $result = $stmt->fetch();
        if(!is_bool($result)){
            $_SESSION['token'] = $_GET["token"];
            $_SESSION['email'] = $result['email'];
            $title = 'Registration';
            $card_name = '新規登録';
            $message = 'ログインを行う前に以下の情報を追加して下さい。';
            $errtype = False;
            if(array_key_exists('err', $_SESSION)){
                $errtype = True;
                $message = $_SESSION['err'];
                unset($_SESSION['err']);
            }

            $email = $result['email'];

            $GAuthJS = <<<EOF
<link href="//cdnjs.cloudflare.com/ajax/libs/cropper/3.1.6/cropper.min.css" rel="stylesheet">
<script src="//cdnjs.cloudflare.com/ajax/libs/cropper/3.1.6/cropper.min.js"></script>
EOF;

            $form = <<<EOF
<form action="Process/RegCheck.php" method="POST" enctype="multipart/form-data">
    <input type='email' name='email' class="form-control" placeholder='メールアドレス' style='margin-bottom: 3%;' value='{$result['email']}' disabled>
    <input type='text' name='username' class="form-control" placeholder='ユーザー名' style='margin-bottom: 3%;'>
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
            header('Location: /AuthSample/login.php');
        }
    }else{
        header('Location: /AuthSample/login.php');
    }
}else{
    header('Location: /AuthSample/login.php');
}