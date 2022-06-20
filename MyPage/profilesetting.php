<?php
/*
 * マイページ（仮、ログイン後）
 */
// 必要ファイルのインクルード
include dirname(__FILE__).'/../Tools/Session.php';
include dirname(__FILE__).'/../Tools/SQL.php';

//セッション開始
SessionStarter();

// ログインしていない、または2段階認証未実施の場合
if(!SessionIsIn('IsAuth') || is_bool(SessionReader('IsAuth')) && !SessionReader('IsAuth')){
    header('Location: /AuthSample/login.php');
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

$name = $get['user_name'];
$pict = $get['user_pict'];

$content = <<< EOF
<div class="box-centering">
    <div class="content-card" style="margin-top: 30px;">
        <div class="menus">
            <div style="text-align: center">
                <div class="bootstrap">
                    <h2>プロフィール設定</h2>
                    <hr>
                </div>
                <a href="/AuthSample/Change/username.php">
                    <div class="selector">
                        <p>ユーザー名の変更</p>
                        <small>ユーザー名を変更します。</small>
                    </div>
                </a>
                <a href="/AuthSample/Change/userpict.php">
                    <div class="selector">
                        <p>ユーザー画像の変更</p>
                        <small>ユーザー画像を変更します。</small>
                    </div>
                </a>
                <a href="/AuthSample/Change/email.php">
                    <div class="selector">
                        <p>メールアドレスの変更</p>
                        <small>現在のメールアドレスを変更します。</small>
                    </div>
                </a>
                <a href="/AuthSample/Change/password.php">
                    <div class="selector">
                        <p>パスワードの変更</p>
                        <small>ログインパスワードを変更します。</small>
                    </div>
                </a>
                <a href="/AuthSample/Change/gauthlink.php">
                    <div class="selector">
                        <p>Googleアカウント連携</p>
                        <small>お使いのアカウントにGoogleアカウントでのログイン機能を追加します。</small>
                    </div>
                </a>
                <a href="/AuthSample/TwoFactorAuthorize.php">
                    <div class="selector">
                        <p>2段階認証の設定</p>
                        <small>Google Authenticatorアプリを使用してログイン時に2段階認証を行えるようにします。</small>
                    </div>
                </a>
                <div>
                    <a href="/AuthSample/Change/del.php" style="color: red;">
                        <div class="selector">
                            <p>アカウントの削除</p>
                            <small>アカウントを削除します。</small>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
EOF;

include dirname(__FILE__).'/../Template/dashboard.php';
?>