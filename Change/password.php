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
                <h1>パスワードの変更</h1>
                <p style='color:{$color}'>{$message}</p>
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
EOF;

include dirname(__FILE__).'/../Template/dashboard.php';
?>