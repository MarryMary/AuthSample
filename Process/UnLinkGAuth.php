<?php
/*
*　ユーザー名変更処理
*/
// 必要ファイルのインクルード
include dirname(__FILE__) . '/../Tools/Session.php';
include dirname(__FILE__) . '/../Tools/SQL.php';

// セッション開始
SessionStarter();

// ログイン状態でない場合はログイン画面へ
if(!SessionIsIn('IsAuth') || is_bool(SessionReader('IsAuth')) && !SessionReader('IsAuth')){
    header('Location: /AuthSample/login.php');
}

// IDを基にUserテーブルからデータを取得
$id = SessionReader('UserId');
$stmt = $pdo->prepare('SELECT * FROM User WHERE id = :id');
$stmt->bindValue(':id', $id, PDO::PARAM_INT);
$res = $stmt->execute();

// SQLが正しく実行できなかった場合
if (!$res) {
    SessionInsert('err', 'エラーが発生しました。もう一度お試し下さい。');
    header('Location: /AuthSample/Change/username.php');
    // SQLが正しく実行できた場合
} else {
    // データ取得
    $get = $stmt->fetch();

    // データが存在する場合
    if (!is_bool($get)) {
        // GAuthID(Google識別用ID)削除
        $stmt = $pdo->prepare('UPDATE User SET GAuthID = :gauthid WHERE id = :id');
        $stmt->bindValue(':gauthid', null, PDO::PARAM_NULL);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $res = $stmt->execute();

        // SQLが正しく実行できた場合
        if ($res) {
            header('Location: /AuthSample/mypage.php');
            // SQLが正しく実行できなかった場合
        } else {
            SessionInsert('err', 'エラーが発生しました。もう一度お試し下さい。');
            header('Location: /AuthSample/Change/gauthlink.php');
        }
    } else {
        header('Location: /AuthSample/Process/Logout.php');
    }
}