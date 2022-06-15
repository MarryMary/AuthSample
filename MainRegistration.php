<?php
include 'Tools/IsInGetTools.php';
SessionStarter();

if(isset($_GET["token"])){
    include 'sql.php';
    $stmt = $pdo->prepare("delete from PreUser WHERE date<=sysdate() - interval 1 day");
    $stmt->bindValue(':token', $_GET["token"], PDO::PARAM_STR);
    $stmt->execute();
    $stmt = $pdo->prepare("SELECT * FROM PreUser WHERE token = :token");
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

            $email = $_SESSION['email'];
            $username = $_SESSION['userid'];

            $GAuthJS = <<<EOF
<link href="//cdnjs.cloudflare.com/ajax/libs/cropper/3.1.6/cropper.min.css" rel="stylesheet">
<script src="//cdnjs.cloudflare.com/ajax/libs/cropper/3.1.6/cropper.min.js"></script>
EOF;

            $form = <<<EOF
<form action="Process/RegCheck.php" method="POST" enctype="multipart/form-data">
    <input type='email' name='email' class="form-control" placeholder='メールアドレス' style='margin-bottom: 3%;' value='{$result['email']}' disabled>
    <input type='text' name='username' class="form-control" placeholder='ユーザー名' style='margin-bottom: 3%;'>
    <input type='password' name='password' class="form-control" placeholder='パスワード' style='margin-bottom: 3%;'>
    <input type='password' name='password' class="form-control" placeholder='パスワード(確認用)' style='margin-bottom: 3%;'>
    <p>プロフィール画像</p>
    <input type="file" name="UserPict" id="UserImage">
    <img id="selectImage" style="max-width:500px;">
    <input type="hidden" id="imageX" name="UserImageX" value="0"/>
    <input type="hidden" id="imageY" name="UserImageY" value="0"/>
    <input type="hidden" id="imageW" name="UserImageW" value="0"/>
    <input type="hidden" id="imageH" name="UserImageH" value="0"/>
    <div style="text-align: center;">
        <button type='submit' class='btn btn-primary' style="width: 80%;">次へ</button>
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
    }else{
        header('Location: /AuthSample/login.php');
    }
}else{
    header('Location: /AuthSample/login.php');
}