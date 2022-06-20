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
    header("Location: /$SERVICE_ROOT/MyPage/home.php");
}

$name = $get['user_name'];
$pict = $get['user_pict'];

if($get['IsTwoFactor'] == 1){
    $parts = <<<EOF
<p>
    2段階認証を無効化します。<br>
    2段階認証を無効化すると、アカウントのセキュリティーが低下する恐れがあります。<br>
    <span style="color: red;">本当に無効化しますか？</span>
</p>

<button type="button" class="btn btn-primary" style="width: 40%;margin-top: 10px;" onclick="location.href='/{$SERVICE_ROOT}/mypage.php'">キャンセル</button>
<button type="button" class="btn btn-danger" style="width: 40%;margin-top: 10px; margin-left: 10px;" onclick="location.href='StartDisable.php'">無効化</button>
EOF;
}else{
    header("Location: /$SERVICE_ROOT/MyPage/home.php");
}

$content = <<< EOF
<div class="box-centering bootstrap">
    <div class="content-card" style="margin-top: 30px;">
        <div class="menu">
            <div style="text-align: center">
                <h1>2段階認証の無効化</h1>
                <p>お使いのアカウントから2段階認証を無効化します。</p>
                <hr>
                {$parts}
            </div>
        </div>
    </div>
</div>
EOF;

include dirname(__FILE__).'/../Template/dashboard.php';
?>