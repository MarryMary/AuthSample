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

$message = 'お使いのアカウントを削除します。';
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
            <button type="button" class="btn btn-primary" style="width: 40%;margin-top: 10px;" onclick="location.href='/AuthSample/MyPage/profilesetting.php'">＜＜ホームに戻る</button>
            <br>
            <div style="text-align: center">
                <h1>アカウント削除</h1>
                <p style='color:{$color}'>{$message}</p>
                <hr>
                <p>
                    お使いのアカウントを削除します。<br>
                    アカウントを削除するとデータが消滅し、以下の機能が利用できなくなります。<br>
                </p>
                    <ul style="list-style-position: inside;">
                        <li>学習データ　</li>
                        <li>進捗情報　　</li>
                        <li>プロフィール</li>
                    </ul>
                <p>
                    また、削除から30日以内に再ログインした場合はアカウントを復活することができます。
                </p>
                <form method='post' action='/AuthSample/Process/DelAccount.php'>
                    <div class="form-check" style="text-align: center; margin-top: 10px; margin-bottom: 10px;">
                        <label class="form-check-label" for="understand">
                            <input class="form-check-input" type="checkbox" name="understand" value="understand" id="understand">
                            上記の内容を理解した
                        </label>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">現在のパスワード</label>
                        <input type="password" class="form-control" id="password" name="password" style="text-align: center;">
                    </div>
                    <button type="submit" class="btn btn-danger" style="width: 40%;margin-top: 10px; margin-left: 10px;">削除</button>
                </form>
            </div>
        </div>
    </div>
</div>
EOF;

include dirname(__FILE__).'/../Template/dashboard.php';
?>