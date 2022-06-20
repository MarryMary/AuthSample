<?php
/*
*　ユーザー名変更処理
*/
// 必要ファイルのインクルード
include dirname(__FILE__).'/../vendor/autoload.php';
include dirname(__FILE__) . '/../Tools/Session.php';
include dirname(__FILE__) . '/../Tools/SQL.php';
include dirname(__FILE__).'/../Template/ServiceData.php';

// セッション開始
SessionStarter();

// ログイン状態でない場合はログイン画面へ
if(!SessionIsIn('IsAuth') || is_bool(SessionReader('IsAuth')) && !SessionReader('IsAuth')){
    header('Location: /AuthSample/login.php');
}

// POST送信の場合
if($_SERVER['REQUEST_METHOD'] == 'POST'){
    // id_tokenが入っているかどうか
    if(isset($_POST['id_token'])){
        // IDを基にUserテーブルからデータを取得
        $id = SessionReader('UserId');
        $stmt = $pdo->prepare('SELECT * FROM User WHERE id = :id');
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $res = $stmt->execute();

        // SQLが正しく実行できなかった場合
        if(!$res){
            SessionInsert('err', 'エラーが発生しました。もう一度お試し下さい。');
            header('Location: /'.$SERVICE_ROOT.'/Change/gauthlink.php');
            // SQLが正しく実行できた場合
        }else{
            // データ取得
            $get = $stmt->fetch();

            // データが存在する場合
            if(!is_bool($get)){
                $id_token = $_POST['id_token'];
                //CLIENT_ID(Google Cloud PlatformのAPIトークン)を定数として定義
                define('CLIENT_ID', '345840626602-q37bp5di0lrr53n3bar423uhg90rff67.apps.googleusercontent.com');
                // GoogleClient(Google API用クラス)のインスタンスを生成
                $client = new Google_Client(['client_id' => CLIENT_ID]);
                // ユーザーIDトークンを認証
                $payload = $client->verifyIdToken($id_token);
                // id_tokenが正しければ
                if($payload){
                    $userid = $payload['sub'];
                    $stmt = $pdo->prepare("UPDATE User SET GAuthID = :id Where id = :userid");
                    $stmt->bindParam( ':id', $userid, PDO::PARAM_STR);
                    $stmt->bindParam( ':userid', $id, PDO::PARAM_INT);
                    $res = $stmt->execute();

                    // SQLが正しく実行できた場合
                    if($res){
                        SessionInsert('err', 'Googleアカウントでの関連付けを行いました。次回ログイン時からGoogleでログイン機能をご利用頂けます。');
                        header('Location: /'.$SERVICE_ROOT.'/Change/gauthlink.php');
                        // SQLが正しく実行できなかった場合
                    }else{
                        SessionInsert('err', 'エラーが発生しました。もう一度お試し下さい。');
                        header('Location: /'.$SERVICE_ROOT.'/Change/gauthlink.php');
                    }
                    // パスワードが間違っている場合
                }else{
                    SessionInsert('err', '不正な値が挿入されました。もう一度お試し下さい。');
                    header('Location: /'.$SERVICE_ROOT.'/Change/gauthlink.php');
                }
                // データが存在しない場合
            }else{
                header('Location: /'.$SERVICE_ROOT.'/Process/Logout.php');
            }
        }
    }else{
        SessionInsert('err', '不正な方法でアクセスされました。もう一度お試し下さい。');
        header('Location: /'.$SERVICE_ROOT.'/Change/gauthlink.php');
    }
// POST送信ではない場合
}else{
    header('Location: /'.$SERVICE_ROOT.'/Auth/login.php');
}