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
                        <h1>パスワードの変更</h1>
                        <p style='color:<?= isset($message) ? 'red' : 'black' ?>'><?=isset($message) ? $message : 'お使いのアカウントのパスワードを変更します。'?></p>
                        <hr>
                        <form method='post' action='/AuthSample/Process/UserPasswordChange.php'>
                            <div class="mb-3">
                                <label for="password_old" class="form-label">現在のパスワード</label>
                                <input type="password" class="form-control" id="password_old" name="password_old" style="text-align: center;">
                            </div>
                            <div class="mb-3">
                                <label for="password1" class="form-label">パスワード</label>
                                <input type="password" class="form-control" id="password1" name="password1" style="text-align: center;">
                                <div id="emailHelp" class="form-text">パスワードは8字以上16字以下で、「?、!、#、,」のいずれかの記号が入っている必要があります。</div>
                            </div>
                            <div class="mb-3">
                                <label for="password2" class="form-label">パスワード(確認用)</label>
                                <input type="password" class="form-control" id="password2" name="password2" style="text-align: center;">
                            </div>
                            <button type="submit" class="btn btn-success" style="width: 40%;margin-top: 10px; margin-left: 10px;">変更</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
</body>
</html>