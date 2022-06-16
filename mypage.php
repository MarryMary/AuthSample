<?php
/*
 * マイページ（仮、ログイン後）
 */
// 必要ファイルのインクルード
include 'Tools/IsInGetTools.php';
include 'Process/sql.php';

//セッション開始
SessionStarter();

// ログインしていない、または2段階認証未実施の場合
if(!isset($_SESSION['IsAuth']) || is_bool($_SESSION['IsAuth']) && !$_SESSION['IsAuth']){
    header('Location: login.php');
}

// ユーザー情報を検索(IsAuthがセッションにあってUserIdがセッションにない状況はありえない(ログイン時・ログアウト時にのみこれらの値が変更される))
$stmt = $pdo->prepare("SELECT * FROM User WHERE id = :id");
$stmt->bindValue(":id", $_SESSION["UserId"], PDO::PARAM_STR);
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
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container-fluid">
            <a class="navbar-brand" href="mypage.php">AuthSample</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="mypage.php">Home</a>
                    </li>
                </ul>
                <ul class="navbar-nav float-end">
                    <img src="<?=$get['user_pict']?>" width="35" height="35" alt="user_profile">
                    <li class="nav-item dropdown" style="float: right;">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <?=$get['user_name']?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                            <li><a class="dropdown-item" href="Process/Logout.php">ログアウト</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <div class="container">
        <div class="back">
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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
</body>
</html>