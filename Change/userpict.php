<?php
/*
 * ユーザー名変更画面
 */
// 必要ファイルのインクルード
include dirname(__FILE__).'/../Tools/Session.php';
include dirname(__FILE__).'/../vendor/autoload.php';
include dirname(__FILE__).'/../Tools/SQL.php';

// セッション開始
SessionStarter();

// もしもログインしていないか2段階認証未実施の場合はログイン画面に遷移
if(!SessionIsIn('IsAuth') || is_bool(SessionReader('IsAuth')) && !SessionReader('IsAuth')){
    header('Location: login.php');
}

// ユーザー情報検索
$stmt = $pdo->prepare("SELECT * FROM User WHERE id = :id");
$stmt->bindValue(":id", $_SESSION["UserId"], PDO::PARAM_STR);
$result = $stmt->execute();
//もしユーザー情報があれば取得
if($result){
    $get = $stmt->fetch();
}else{
    // なければマイページに遷移
    header("Location: mypage.php");
}

if(SessionIsIn('err')){
    $message = SessionReader('err');
    SessionUnset('err');
}
?>
<!DOCTYPE html>
<html lang='ja'>
<head>
    <meta charset='UTF-8'>
    <meta http-equiv='X-UA-Compatible' content='IE=edge'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>MyPage</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link href="//cdnjs.cloudflare.com/ajax/libs/cropper/3.1.6/cropper.min.css" rel="stylesheet">
    <script src="//cdnjs.cloudflare.com/ajax/libs/cropper/3.1.6/cropper.min.js"></script>
    <link rel="stylesheet" href="/AuthSample/CSS/style.css">
</head>
<body>
<nav class="navbar navbar-light bg-light">
    <div class="container-fluid">
        <a class="navbar-brand" href="#">Sample</a>
    </div>
</nav>
<div class="container">
    <div class="glass">
        <div class="row">
            <div class="col-sm-3">
                <div class="profile">
                    <img src="<?=$get['user_pict']?>" width="200" height="200" alt="user_profile">
                    <h2><?=$get['user_name']?></h2>
                    <button type="button" name="button" class="btn btn-primary" onclick="location.href='/AuthSample/Process/Logout.php'" style="width: 90%; margin: 10px;">ログアウト</button>
                </div>
            </div>
            <div class="col-sm-9">
                <div class="menu">
                    <button type="button" class="btn btn-primary" style="width: 40%;margin-top: 10px;" onclick="location.href='/AuthSample/mypage.php'">＜＜ホームに戻る</button>
                    <br>
                    <div style="text-align: center">
                        <h1>ユーザー画像の変更</h1>
                        <p style='color:<?= isset($message) ? 'red' : 'black' ?>'><?=isset($message) ? $message : 'お使いのアカウントのユーザー画像を変更します。'?></p>
                        <hr>
                        <form method='post' action='/AuthSample/Process/UserPictChange.php' enctype="multipart/form-data">
                            <input type="file" name="UserPict" id="UserImage">
                            <img id="selectImage" style="max-width:500px;">
                            <input type="hidden" id="imageX" name="UserImageX" value="0"/>
                            <input type="hidden" id="imageY" name="UserImageY" value="0"/>
                            <input type="hidden" id="imageW" name="UserImageW" value="0"/>
                            <input type="hidden" id="imageH" name="UserImageH" value="0"/>
                            <div class="mb-3">
                                <label for="password" class="form-label">パスワード</label>
                                <input type="password" class="form-control" id="password" name="password" style="text-align: center;">
                            </div>
                            <button type="submit" class="btn btn-success" style="width: 40%;margin-top: 10px; margin-left: 10px;">変更</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.6.0.js" integrity="sha256-H+K7U5CnXl1h5ywQfKtSj8PCmoN9aaq30gDh27Xc0jk=" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.12/cropper.js" type="text/javascript"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-cropper/1.0.1/jquery-cropper.js" type="text/javascript"></script>
<script src="/AuthSample/JavaScript/Register.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
</body>
</html>