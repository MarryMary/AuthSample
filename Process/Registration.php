<?php
/*
 * 新規登録処理を実施するファイル
 */
// 必要ファイルのインクルード
include dirname(__FILE__).'/../Tools/IsInGetTools.php';
include dirname(__FILE__).'/../Tools/SQL.php';

// セッションの開始
SessionStarter();

// POST送信かどうか
if($_SERVER['REQUEST_METHOD'] == 'POST'){
    // 登録に必要な値がセッションに入っているか
    if(SessionIsIn('filename') && SessionIsIn('username') && SessionIsIn('password') && SessionIsIn('email')){
        // 削除フラグを0に（削除していない状態）
        $flag = 0;

        // 必要な値を変数に代入
        $password = password_hash(SessionReader('password'), PASSWORD_DEFAULT);
        $token = SessionReader('token');
        $email = SessionReader('email');
        $username = SessionReader('username');
        $filename = SessionReader('filename');

        // データをユーザーテーブルにインサート
        $stmt = $pdo->prepare("INSERT INTO User (email, pass, user_name,user_pict, delete_flag) VALUES (:email, :pass, :user_name,:user_pict, :delete_flag)");
        $stmt->bindParam( ':email', $email, PDO::PARAM_STR);
        $stmt->bindParam( ':pass', $password, PDO::PARAM_STR);
        $stmt->bindParam( ':user_name', $username, PDO::PARAM_STR);
        $stmt->bindParam( ':user_pict', $filename, PDO::PARAM_STR);
        $stmt->bindParam( ':delete_flag', $flag, PDO::PARAM_INT);
        $res = $stmt->execute();

        // SQLが正しく実行できた場合
        if($res){
            // 仮ユーザーテーブルからデータを削除
            $stmt = $pdo->prepare("DELETE FROM PreUser WHERE user_token = :user_token");
            $stmt->bindParam( ':user_token', $_SESSION['token'], PDO::PARAM_STR);
            $res = $stmt->execute();

            SessionInsert('registration', True);
            header('Location: /AuthSample/regfinish.php');
        // SQLが正しく実行できなかった場合
        }else{
            header('Location: /AuthSample/login.php');
        }
    // 必要な値がセッションに入っていない場合
    }else{
        header('Location: /AuthSample/login.php');
    }
// POST送信ではない場合
}else{
    header('Location: /AuthSample/login.php');
}