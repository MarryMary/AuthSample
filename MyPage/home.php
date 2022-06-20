<?php
/*
 * マイページ（仮、ログイン後）
 */
// 必要ファイルのインクルード
include dirname(__FILE__).'/../Tools/Session.php';
include dirname(__FILE__).'/../Tools/SQL.php';
include dirname(__FILE__).'/../Template/ServiceData.php';

//セッション開始
SessionStarter();

// ログインしていない、または2段階認証未実施の場合
if(!SessionIsIn('IsAuth') || is_bool(SessionReader('IsAuth')) && !SessionReader('IsAuth')){
    header('Location: /'.$SERVICE_ROOT.'/Auth/login.php');
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
    <div class="content-card">
        <div class="text-centering">
            <div class="profile-img-parent">
                <img src="{$pict}" alt="ユーザー画像" style="width: 100%;">
            </div>
            <h1 style="font-size: 50px;" class="progress-text">{$name}</h1>
            <div class="progress">
                <h2 class="progress-text">
                    問題回答率 85 / 100 | 正答率 85% | ランク A
                </h2>
            </div>
        </div>
    </div>
</div>

<div class="box-centering">
    <div class="content-card" style="margin-top: 30px;">
        <div class="text-centering bootstrap">
            <h2>{$name}さんにおすすめのカリキュラム</h2>
            <h4>4件のおすすめがあります</h4>
        </div>
        <hr>
        <div class="menus">
            <a href="/{$SERVICE_ROOT}/Change/username.php">
                <div class="selector">
                    <p style="font-size: 20px;">あのカレー食えねぇじゃねぇかよ</p>
                    <small>平均解答時間：10分 問題種別：数学</small>
                </div>
                <div class="selector">
                    <p style="font-size: 20px;">お前に慈悲は与えない！</p>
                    <small>平均解答時間：10分 問題種別：PHPコーディングテスト</small>
                </div>
                <div class="selector">
                    <p style="font-size: 20px;">風林火山&#129306;</p>
                    <small>平均解答時間：10分 問題種別：アルゴリズム</small>
                </div>
                <div class="selector">
                    <p style="font-size: 20px;">この人の言うことを信じちゃ駄目だ！&#128073;</p>
                    <small>平均解答時間：10分 問題種別：Goコーディングテスト</small>
                </div>
            </a>
        </div>
    </div>
</div>
EOF;

include dirname(__FILE__).'/../Template/dashboard.php';
?>