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
$userid = SessionReader('UserId');
$stmt = $pdo->prepare("SELECT * FROM User WHERE id = :id");
$stmt->bindValue(":id", $userid, PDO::PARAM_STR);
$result = $stmt->execute();
//もしユーザー情報があれば取得
if($result){
    $get = $stmt->fetch();
}else{
    // なければマイページに遷移
    header("Location: mypage.php");
}

$name = $get['user_name'];
$pict = $get['user_pict'];

$message = 'お使いのアカウントのユーザー名を変更します。';
$color = 'black';

if(SessionIsIn('err')){
    $message = SessionReader('err');
    $color = 'red';
    SessionUnset('err');
}


$content = <<<EOF
<div class="box-centering bootstrap">
    <div class="content-card" style="margin-top: 30px;">
        <div class="menu">
            <button type="button" class="btn btn-primary" style="width: 40%;margin-top: 10px;" onclick="location.href='/AuthSample/MyPage/ProfileSetting.php'">＜＜ホームに戻る</button>
            <br>
            <div style="text-align: center">
                <h1>ユーザー画像の変更</h1>
                <p style='color:{$color}'>{$message}</p>
                <hr>
                <form method='post' action='/AuthSample/Process/UserPictChange.php' enctype="multipart/form-data" class="non-radius">
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
EOF;

$css = <<<EOF
<link href="//cdnjs.cloudflare.com/ajax/libs/cropper/3.1.6/cropper.min.css" rel="stylesheet">
<script src="//cdnjs.cloudflare.com/ajax/libs/cropper/3.1.6/cropper.min.js"></script>
EOF;

$js = <<<EOF
<script src="https://code.jquery.com/jquery-3.6.0.js" integrity="sha256-H+K7U5CnXl1h5ywQfKtSj8PCmoN9aaq30gDh27Xc0jk=" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.12/cropper.js" type="text/javascript"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-cropper/1.0.1/jquery-cropper.js" type="text/javascript"></script>
<script src="/AuthSample/JavaScript/Register.js"></script>
EOF;

include dirname(__FILE__).'/../Template/dashboard.php';
?>