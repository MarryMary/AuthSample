<?php
/*
 * Google Authenticatorによる2段階認証のコード確認
 */
// 必要ファイルのインクルード
include dirname(__FILE__).'/../vendor/autoload.php';
include dirname(__FILE__).'/../Tools/Session.php';
include dirname(__FILE__).'/../Tools/SQL.php';
include dirname(__FILE__).'/../Template/ServiceData.php';

// セッションの開始
SessionStarter();

// 既にログイン状態であるかログインすらしていない場合
if(!SessionIsIn('IsAuth') || !SessionIsIn('NeedTwoFactor') || SessionIsIn('IsAuth') && is_bool(SessionReader('IsAuth')) && SessionReader('IsAuth')){
    header("location: /$SERVICE_ROOT/MyPage/home.php");
}

// ユーザー情報をidから検索
$id = SessionReader('UserId');
$stmt = $pdo->prepare("SELECT * FROM User WHERE id = :id");
$stmt->bindValue(":id", $id, PDO::PARAM_INT);
$res = $stmt->execute();

// SQLが正しく実行できなかった場合
if(!$res){
    header("Location: /$SERVICE_ROOT/Process/Logout.php");
// SQLが正しく実行できた場合
}else{
    // データを取得
    $data = $stmt->fetch();

    // データが存在しなかった場合
    if(is_bool($data)){
        header("Location: /$SERVICE_ROOT/Process/Logout.php");

    // データが存在する場合
    }else{
        // Google Authenticatorライブラリをインスタンス化
        $ga = new PHPGangsta_GoogleAuthenticator();
        // 2段階認証の秘密鍵を取得
        $secret = $data["TwoFactorSecret"];
        // 送信されたトークンを取得
        $code = filter_input(INPUT_POST, 'token');
        // 30sec * 2(1min)　サーバーとクライアントの時間のずれを許容する
        $discrepancy = 2;

        // コードを認証
        $checkResult = $ga->verifyCode($secret, $code, $discrepancy);
        // 認証できれば
        if($checkResult){

            // ログインさせる
            SessionInsert('IsAuth', True);
            SessionUnset('2Factor-Token');
            SessionUnset('2Factor-Token');
            header("Location: /$SERVICE_ROOT/MyPage/home.php");
        }else{
            SessionInsert('err', 'コードが異なります');
            header("Location: /$SERVICE_ROOT/TwoFactor/GoogleAuthenticator.php");
        }
    }
}