<?php
/*
 * マイページ（仮、ログイン後）
 */
// 必要ファイルのインクルード
include 'Tools/Session.php';
include 'Tools/SQL.php';

//セッション開始
SessionStarter();

// ログインしていない、または2段階認証未実施の場合
if(!SessionIsIn('IsAuth') || is_bool(SessionReader('IsAuth')) && !SessionReader('IsAuth')){
    header('Location: login.php');
}

// ユーザー情報を検索(IsAuthがセッションにあってUserIdがセッションにない状況はありえない(ログイン時・ログアウト時にのみこれらの値が変更される))
$userid = SessionReader('UserId');
$stmt = $pdo->prepare("SELECT * FROM User WHERE id = :id");
$stmt->bindValue(":id", $userid, PDO::PARAM_STR);
$result = $stmt->execute();

// SQLが実行できた場合
if($result){
    // 全件取得
    $get = $stmt->fetch();
}else {
    // SQLが実行できなかった場合
    echo "Fatal Error: サーバー管理者にお問い合わせ下さい。";
    exit;
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
                            <a href="Change/username.php">
                                <div class="selector">
                                    <p>ユーザー名の変更</p>
                                    <small>ユーザー名を変更します。</small>
                                </div>
                                <hr>
                            </a>
                            <a href="Change/userpict.php">
                                <div class="selector">
                                    <p>ユーザー画像の変更</p>
                                    <small>ユーザー画像を変更します。</small>
                                </div>
                                <hr>
                            </a>
                            <a href="Change/email.php">
                                <div class="selector">
                                    <p>メールアドレスの変更</p>
                                    <small>現在のメールアドレスを変更します。</small>
                                </div>
                                <hr>
                            </a>
                            <a href="Change/password.php">
                                <div class="selector">
                                    <p>パスワードの変更</p>
                                    <small>ログインパスワードを変更します。</small>
                                </div>
                                <hr>
                            </a>
                            <a href="Change/gauthlink.php">
                                <div class="selector">
                                    <p>Googleアカウント連携</p>
                                    <small>お使いのアカウントにGoogleアカウントでのログイン機能を追加します。</small>
                                </div>
                                <hr>
                            </a>
                            <a href="TwoFactorAuthorize.php">
                                <div class="selector">
                                    <p>2段階認証の設定</p>
                                    <small>Google Authenticatorアプリを使用してログイン時に2段階認証を行えるようにします。</small>
                                </div>
                                <hr>
                            </a>
                            <div style="margin-top: 10%;">
                                <h2>操作危険範囲</h2>
                                <hr>
                            </div>
                            <div style="margin-top: 10%;">
                                <hr>
                                <a href="Change/del.php" style="color: red;">
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