<?php
/*
* ログイン処理の実行ファイル
*/

//必要ファイルのインクルード
include dirname(__FILE__).'/../Tools/Session.php';
include dirname(__FILE__).'/../Tools/SQL.php';

//セッション開始
SessionStarter();

// emailとpasswordフィールドに送信されているか、または空文字でないか
if(SessionIsIn('Recover') && SessionIsIn('UserId')){
    // 復元ボタンが押された場合
    if(!isset($_GET['cancel'])){
        // 論理削除から30日が経過したユーザーを物理削除
        $stmt = $pdo->prepare("delete from User WHERE delete_flag = 1 AND delete_at<=sysdate() - interval 30 day");
        $stmt->execute();
        
        $id = SessionReader('UserId');

        // メールアドレスを基にデータを取得
        $stmt = $pdo->prepare("SELECT * FROM User WHERE id = :id");
        $stmt->bindParam( ':id', $id, PDO::PARAM_INT);
        $res = $stmt->execute();

        // SQLが実行できた場合
        if($res) {
            // データを取得
            $data = $stmt->fetch();
            // ユーザー情報が見つかり、そのユーザーが論理削除状態であった場合
            if(!is_bool($data) && $data['delete_flag'] == 1){
                $today = null;
                $stmt = $pdo->prepare("UPDATE User SET delete_flag = 0, delete_at = :today WHERE id = :id");
                $stmt->bindParam( ':today', $today, PDO::PARAM_NULL);
                $stmt->bindParam( ':id', $id, PDO::PARAM_INT);
                $res = $stmt->execute();

                SessionUnset();
                SessionInsert('err', 'アカウントが復元されました。再度ログインして下さい。');
                header('Location: /AuthSample/login.php');
            // ユーザーが論理削除ではないか、ユーザーが見つからなかった場合    
            }else{
                SessionUnset();
                header('Location: /AuthSample/login.php');
            }
        }else{
            // SQLが正しく実行できなかった場合
            SessionInsert('err', 'エラーが発生しました。もう一度お試し下さい。');
            header('Location: /AuthSample/login.php');
        }
        // PDO接続解除
        $pdo = null;
    // キャンセルボタンが押された場合
    }else{
        SessionUnset();
        header('Location: /AuthSample/login.php');
    }
}else{
    SessionUnset();
    header('Location: /AuthSample/login.php');
}