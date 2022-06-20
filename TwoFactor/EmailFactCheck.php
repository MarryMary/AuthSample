<?php
/*
 * メールで送信された2段階認証コードを検証するファイルです。
 */
// 必要ファイルのインクルード
include dirname(__FILE__).'/../Tools/Session.php';
include dirname(__FILE__).'/../Template/ServiceData.php';

// POST送信であれば
if($_SERVER["REQUEST_METHOD"] === "POST"){

    // セッション開始
    SessionStarter();

    // 2段階認証トークンがセッションとポスト送信両方に存在し、それらが等しい場合
    if(SessionIsIn('2Factor-Token') && isset($_POST["token"]) && trim(SessionReader('2Factor-Token')) == trim($_POST["token"])){
        SessionInsert('IsAuth', True);
        SessionUnset('2Factor-Token');
        header("Location: /$SERVICE_ROOT/MyPage/home.php");
    // もしコードが送信されていないか2段階認証トークンが存在しない場合
    }else{
        SessionInsert('err', 'コードが異なります。');
        header("Location: /$SERVICE_ROOT/TwoFactor/MailFactorSend.php");
    }
// POST送信でなければ
}else{
    header("Location: /$SERVICE_ROOT/Auth/login.php");
}