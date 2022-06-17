<?php
/*
 * 2段階認証を無効化するか確認するための画面
 */
// 必要ファイルのインクルード
include dirname(__FILE__).'/../Tools/Session.php';
include dirname(__FILE__).'/../vendor/autoload.php';
include dirname(__FILE__).'/../Tools/SQL.php';
include dirname(__FILE__).'/../Template/ServiceData.php';

// セッション開始
SessionStarter();

// ログイン状態でない場合
if(!SessionIsIn('IsAuth') || is_bool(SessionReader('IsAuth')) && !SessionReader('IsAuth')){
    header('Location: login.php');
}

// ユーザー情報の取得
$userid = SessionReader('UserId');
$stmt = $pdo->prepare("SELECT * FROM User WHERE id = :id");
$stmt->bindValue(":id", $userid, PDO::PARAM_STR);
$result = $stmt->execute();
// SQLが正しく実行できた場合
if($result){
    $get = $stmt->fetch();
//SQLが正しく実行できなかった場合
}else{
    header("Location: mypage.php");
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
                    <button type="button" name="button" class="btn btn-primary" onclick="location.href='Process/Logout.php'" style="width: 90%; margin: 10px;">ログアウト</button>
                </div>
            </div>
            <div class="col-sm-9">
                <div class="menu">
                    <div style="text-align: center">
                        <h1>2段階認証の無効化</h1>
                        <p>お使いのアカウントから2段階認証を無効化します。</p>
                        <hr>
                        <?php
                        // 2段階認証が有効であれば
                        if($get['IsTwoFactor'] == 1):
                            ?>
                            <p>
                                2段階認証を無効化します。<br>
                                2段階認証を無効化すると、アカウントのセキュリティーが低下する恐れがあります。<br>
                                <span style="color: red;">本当に無効化しますか？</span>
                            </p>

                            <button type="button" class="btn btn-primary" style="width: 40%;margin-top: 10px;" onclick="location.href='/AuthSample/mypage.php'">キャンセル</button>
                            <button type="button" class="btn btn-danger" style="width: 40%;margin-top: 10px; margin-left: 10px;" onclick="location.href='StartDisable.php'">無効化</button>
                        <?php
                            // 2段階認証が無効であれば
                            else:
                                header("Location: /$SERVICE_ROOT/mypage.php");
                            endif;
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
</body>
</html>