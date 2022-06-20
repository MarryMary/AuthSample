<?php
/*
 * Googleアカウントをベースとしたアカウント登録を行う
 */
// 必要ファイルのインクルード
include dirname(__FILE__).'/../Tools/Session.php';
include dirname(__FILE__).'/../Tools/SQL.php';
include dirname(__FILE__).'/../Template/ServiceData.php';

// セッション開始
SessionStarter();

// POST送信されている場合
if($_SERVER['REQUEST_METHOD'] == 'POST'){
    // セッションに登録に必要な情報が入っているかどうか
    if(SessionIsIn('filename') && SessionIsIn('username') && SessionIsIn('password') && SessionIsIn('userid') && SessionIsIn('email')){
        // 削除フラグ（削除されているかどうか）を0に(削除状態ではない)
        $flag = 0;

        // 必要な値をセッションから取得し変数に代入
        $email = SessionReader('email');
        $username = SessionReader('username');
        $password = password_hash(SessionReader('password'), PASSWORD_DEFAULT);
        $filename = SessionReader('filename');
        $userid = SessionReader('userid');

        // Userテーブルにそれぞれの値をインサート
        $stmt = $pdo->prepare("INSERT INTO User (email, pass, user_name,user_pict, GAuthID, delete_flag) VALUES (:email, :pass, :user_name,:user_pict, :gauth_id, :delete_flag)");
        $stmt->bindParam( ':email', $email, PDO::PARAM_STR);
        $stmt->bindParam( ':user_name', $username, PDO::PARAM_STR);
        $stmt->bindParam( ':pass', $password, PDO::PARAM_STR);
        $stmt->bindParam( ':user_pict', $filename, PDO::PARAM_STR);
        $stmt->bindParam( ':gauth_id', $userid, PDO::PARAM_STR);
        $stmt->bindParam( ':delete_flag', $flag, PDO::PARAM_INT);
        $res = $stmt->execute();

        // SQLが正しく実行された場合
        if($res){
            // 登録完了フラグをセッションに代入し、登録完了画面に遷移
            SessionInsert('registration', True);
            header('Location: /'.$SERVICE_ROOT.'/Auth/regfinish.php');
        }else{
            // SQLが正しく実行できなかった場合
            header('Location: /'.$SERVICE_ROOT.'/Auth/login.php');
        }
    }else{
        // 必要情報がセッションに入っていなかった場合
        header('Location: /'.$SERVICE_ROOT.'/Auth/login.php');
    }
}else{
    // POST送信ではなかった場合
    header('Location: /'.$SERVICE_ROOT.'/Auth/login.php');
}