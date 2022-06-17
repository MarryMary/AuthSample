<?php
/*
 * 2段階認証の設定完了画面に遷移するためのファイル
 */
// 必要ファイルのインクルード
include dirname(__FILE__).'/../vendor/autoload.php';
include dirname(__FILE__).'/../Tools/Session.php';
include dirname(__FILE__).'/../Tools/ValidateAndSecure.php';
include dirname(__FILE__).'/../Tools/SQL.php';
include dirname(__FILE__).'/../Template/ServiceData.php';

// セッション開始
SessionStarter();


// ログイン状態でない場合
if(!SessionIsIn('IsAuth') || SessionIsIn('IsAuth') && is_bool(SessionReader('IsAuth')) && !SessionReader('IsAuth')){
    header("location: /$SERVICE_ROOT/mypage.php");
}

// ユーザーテーブルをidから検索
$userid = SessionReader('UserId');
$stmt = $pdo->prepare("SELECT * FROM User WHERE id = :id");
$stmt->bindValue(":id", $userid, PDO::PARAM_INT);
$res = $stmt->execute();

// SQLが正しく実行できなかった場合
if(!$res){
    header("Location: /$SERVICE_ROOT/Process/Logout.php");
// SQLが正しく実行できた場合
}else{
    // データ取得
    $data = $stmt->fetch();
    
    // データが存在しなかった場合
    if(is_bool($data)){
        header("Location: /$SERVICE_ROOT/Process/Logout.php");
    // データが存在する場合
    }else{
        // Google Authenticatorインスタンスを生成
        $ga = new PHPGangsta_GoogleAuthenticator();
        // 2段階認証のトークンを取得
        $secret = $data["TwoFactorSecret"];
        // ユーザーが送信したトークンを取得
        $code = filter_input(INPUT_POST, 'token');
        // 30sec * 2( = 1min)、サーバーとクライアントの時間のズレを許容する
        $discrepancy = 2;

        // コードを検証
        $checkResult = $ga->verifyCode($secret, $code, $discrepancy);

        // トークンが正しい場合
        if($checkResult){
            // 仮ユーザーテーブルからデータを削除
            $stmt = $pdo->prepare("DELETE FROM PreUser WHERE user_token = :token");
            $stmt->bindValue(":token", $_SESSION["token"], PDO::PARAM_STR);
            $result = $stmt->execute();


            $title = 'Google Authenticator Enabled';
            $card_name = '設定完了';
            $message = '全ての設定が完了しました！';
            $errtype = False;
            if(SessionIsIn('err')){
                $errtype = True;
                $message = SessionReader('err');
                SessionUnset('err');
            }

            $form = <<<EOF
<p>
    全ての設定が完了しました。<br>
    次回認証時から2段階認証が有効化されます。<br>
    <button type="button" class="btn btn-primary" onclick="location.href='/AuthSample/mypage.php'" style="width: 90%;">ホームへ</button>
</p>
EOF;

            $scriptTo = 'JavaScript/Login.js';
            $JS = '<script src="https://unpkg.com/jwt-decode/build/jwt-decode.js"></script>';

            include dirname(__FILE__).'/../Template/BaseTemplate.php';
        }else{
            SessionInsert('err', 'コードが異なります');
            header("Location: /AuthSample/TwoFactor/EnableTwoFactor.php?token=".$_SESSION["token"]);
        }
    }
}