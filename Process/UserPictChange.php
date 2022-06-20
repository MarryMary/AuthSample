<?php
/*
*　ユーザー名変更処理
*/
// 必要ファイルのインクルード
include dirname(__FILE__) . '/../Tools/Session.php';
include dirname(__FILE__) . '/../Tools/SQL.php';
include dirname(__FILE__).'/../Template/ServiceData.php';

// セッション開始
SessionStarter();

// ログイン状態でない場合はログイン画面へ
if(!SessionIsIn('IsAuth') || is_bool(SessionReader('IsAuth')) && !SessionReader('IsAuth')){
    header('Location: /'.$SERVICE_ROOT.'/Auth/login.php');
}

// POST送信の場合
if($_SERVER['REQUEST_METHOD'] == 'POST'){
    // ユーザー名とパスワードが入っているかどうか
    if(isset($_POST['password']) && trim($_POST['password']) != ''){
        // IDを基にUserテーブルからデータを取得
        $id = SessionReader('UserId');
        $stmt = $pdo->prepare('SELECT * FROM User WHERE id = :id');
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $res = $stmt->execute();

        // SQLが正しく実行できなかった場合
        if(!$res){
            SessionInsert('err', 'エラーが発生しました。もう一度お試し下さい。');
            header('Location: /'.$SERVICE_ROOT.'/Change/userpict.php');
            // SQLが正しく実行できた場合
        }else{
            // データ取得
            $get = $stmt->fetch();

            // データが存在する場合
            if(!is_bool($get)){
                // パスワードを確認
                if(password_verify($_POST['password'], $get['pass'])){
                    include dirname(__FILE__) . '/../Tools/Uploader.php';
                    // ファイルがアップロードされており、$file変数に値が入っている場合
                    if(isset($file)) {
                        // ユーザー名をアップデート
                        $stmt = $pdo->prepare('UPDATE User SET user_pict = :userpict WHERE id = :id');
                        $stmt->bindValue(':userpict', $file, PDO::PARAM_STR);
                        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
                        $res = $stmt->execute();

                        // SQLが正しく実行できた場合
                        if ($res) {
                            header('Location: /'.$SERVICE_ROOT.'/MyPage/profilesetting.php');
                            // SQLが正しく実行できなかった場合
                        } else {
                            SessionInsert('err', 'エラーが発生しました。もう一度お試し下さい。');
                            header('Location: /'.$SERVICE_ROOT.'/Change/userpict.php');
                        }
                    //画像のアップロードに失敗した場合
                    }else{
                        SessionInsert('err', '画像のアップロードに失敗しました。');
                        header('Location: /'.$SERVICE_ROOT.'/Change/userpict.php');
                    }
                    // パスワードが間違っている場合
                }else{
                    SessionInsert('err', 'パスワードが間違っています。');
                    header('Location: /'.$SERVICE_ROOT.'/Change/userpict.php');
                }
                // データが存在しない場合
            }else{
                header('Location: /'.$SERVICE_ROOT.'/Process/Logout.php');
            }
        }
    }else{
        SessionInsert('err', 'パスワードが入力されていません。');
        header('Location: /'.$SERVICE_ROOT.'/Change/userpict.php');
    }
// POST送信ではない場合
}else{
    header('Location: /'.$SERVICE_ROOT.'/Auth/login.php');
}