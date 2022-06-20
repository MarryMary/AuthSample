<?php
/*
 * ユーザー名変更画面
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

$message = 'お使いのアカウントのユーザー名を変更します。';
$color = 'black';

if(SessionIsIn('err')){
    $message = SessionReader('err');
    $color = 'red';
    SessionUnset('err');
}

$content = <<< EOF
<div class="box-centering bootstrap">
    <div class="content-card" style="margin-top: 30px;">
        <div class="mens">
            <button type="button" class="btn btn-primary" style="width: 40%;margin-top: 10px;" onclick="location.href='/AuthSample/MyPage/ProfileSetting.php'">＜＜ホームに戻る</button>
            <br>
            <div style="text-align: center">
                <h1>ユーザー名の変更</h1>
                <p style='color:{$color}'>{$message}</p>
                <hr>
                <form method='post' action='/AuthSample/Process/UserNameChange.php'>
                    <div class="mb-3">
                        <label for="UserName" class="form-label">新しいユーザー名</label>
                        <input type="text" class="form-control" id="UserName" name="username" style="text-align: center;">
                    </div>
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

include dirname(__FILE__).'/../Template/dashboard.php';
?>