<?php
include 'Tools/IsInGetTools.php';
SessionStarter();
if(!isset($_SESSION['IsAuth']) || is_bool($_SESSION['IsAuth']) && !$_SESSION['IsAuth']){
    header('Location: login.php');
}

include 'Process/sql.php';
$stmt = $pdo->prepare("SELECT * FROM User WHERE id = :id");
$stmt->bindValue(":id", $_SESSION["UserId"], PDO::PARAM_STR);
$result = $stmt->execute();
if($result){
    $get = $stmt->fetch();
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
    <link rel="stylesheet" href="CSS/style.css">
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
                            <h1>マイメニュー</h1>
                            <hr>
                            <a href="TwoFactorAuthorize.php">
                                <div class="selector">
                                    <p>2段階認証の設定</p>
                                    <small>Google Authenticatorアプリを使用してログイン時に2段階認証を行えるようにします。</small>
                                </div>
                                <hr>
                            </a>
                            <a href="#">
                                <div class="selector">
                                    <p>メールアドレスの更新</p>
                                    <small>現在のメールアドレスを更新します。</small>
                                </div>
                                <hr>
                            </a>
                            <a href="#">
                                <div class="selector">
                                    <p>Googleアカウント連携</p>
                                    <small>お使いのアカウントにGoogleアカウントでのログイン機能を追加します。</small>
                                </div>
                                <hr>
                            </a>
                            <a href="#">
                                <div class="selector">
                                    <p>パスワードの更新</p>
                                    <small>現在のパスワードを更新します。</small>
                                </div>
                                <hr>
                            </a>
                            <div style="margin-top: 10%;">
                                <h2>操作危険範囲</h2>
                                <hr>
                            </div>
                            <div style="margin-top: 10%;">
                                <hr>
                                <a href="#" style="color: red;">
                                    <div class="selector">
                                        <p>アカウントの削除</p>
                                        <small>アカウントを削除します。</small>
                                    </div>
                                </a>
                                <hr>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
</body>
</html>