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

$GLinked = False;
$title = 'Googleアカウントとの連携';
$message = 'あなたのアカウントをGoogleアカウントと連携します。';
if(!is_null($get['GAuthID'])){
    $title = 'Googleアカウントとの連携解除';
    $message = 'あなたのアカウントとGoogleアカウントとの連携を解除します。';
    $GLinked = True;
}

if($GLinked){

    $paste = <<<EOF
    <p>
    あなたのアカウントは現在Googleアカウントと関連付いています。<br>
    Googleアカウントとの関連付けを解除するには以下のボタンを押して下さい。<br>
    また、現在のメールアドレスがGoogleアカウントのものと同じであり、Googleアカウントが現在も存在する場合は、ログイン画面でGoogleでログインを選択すると再度関連付いてしまいますのでご注意下さい。
</p>
<div style="text-align: center;">
    <button type="button" class="btn btn-danger" onclick="location.href='/AuthSample/Process/UnLinkGAuth.php'" style="width: 90%;">関連付け解除</button>
</div>
EOF;

}else{
    $paste = <<<EOF
    <p>
    あなたのアカウントは現在Googleアカウントと関連付いていません。<br>
    Googleアカウントとの関連付けを行うには以下のボタンを押して下さい。
</p>
<br>
<div style="text-align: center;">
    <div class="g_id_signin"
        data-type="standard"
        data-size="large"
        data-theme="outline"
        data-text="sign_in_with"
        data-shape="rectangular"
        data-logo_alignment="left">
    </div>
</div>
EOF;
}

$content = <<<EOF
<div class="box-centering bootstrap">
    <div class="content-card" style="margin-top: 30px;">
        <div class="menu">
            <button type="button" class="btn btn-primary" style="width: 40%;margin-top: 10px;" onclick="location.href='/AuthSample/MyPage/profilesetting.php'">＜＜ホームに戻る</button>
            <br>
            <div style="text-align: center">
                <h1>{$title}</h1>
                <p style='color:{$color}'>{$message}</p>
                <hr>
                {$paste}
            </div>
        </div>
    </div>
</div>
EOF;

include dirname(__FILE__).'/../Template/dashboard.php';
?>