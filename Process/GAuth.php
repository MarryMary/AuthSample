<?php
/*
* Googleシングルサインオン処理ファイル
*/
// 必要ファイルのインクルード
include dirname(__FILE__).'/../vendor/autoload.php';
include dirname(__FILE__).'/../Tools/Session.php';
include dirname(__FILE__).'/../Tools/SQL.php';

SessionStarter();
// POST送信されており、Googleのトークンがあるかどうか確認
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id_token'])){
    $id_token = $_POST['id_token'];
    //CLIENT_ID(Google Cloud PlatformのAPIトークン)を定数として定義
    define('CLIENT_ID', '345840626602-q37bp5di0lrr53n3bar423uhg90rff67.apps.googleusercontent.com');
    // GoogleClient(Google API用クラス)のインスタンスを生成
    $client = new Google_Client(['client_id' => CLIENT_ID]);
    // ユーザーIDトークンを認証
    $payload = $client->verifyIdToken($id_token);
    // トークンが正しい（Googleアカウントがあれば）
    if ($payload) {
        // ユーザー認識用ID（Googleアカウント識別用ID）を取得
        $userid = $payload['sub'];
        // 同じIDを持つアカウントがないか検索
        $stmt = $pdo->prepare("SELECT * FROM User WHERE GAuthID = :id");
        $stmt->bindParam( ':id', $userid, PDO::PARAM_STR);
        $res = $stmt->execute();

        // 正しくSQLが実行された場合
        if($res){
            // データを取得
            $data = $stmt->fetch();
            // データがなかった場合
            if(is_bool($data)){
                // Googleのメールアドレスで再検索
                $stmt = $pdo->prepare("SELECT * FROM User WHERE email = :email");
                $stmt->bindParam( ':email', $payload['email'], PDO::PARAM_STR);
                $res = $stmt->execute();
                // 正しくSQLが実行された場合
                if($res){
                    // データを取得
                    $data = $stmt->fetch();
                    // データがあれば
                    if(!is_bool($data)){
                        // Google アカウントの識別用IDをテーブルにインサートして関連付け
                        $userid = $payload['sub'];
                        $stmt = $pdo->prepare("UPDATE User SET GAuthID = :id Where email = :email");
                        $stmt->bindParam( ':email', $payload['email'], PDO::PARAM_STR);
                        $stmt->bindParam( ':id', $userid, PDO::PARAM_STR);
                        $res = $stmt->execute();
                        // 2段階認証が有効になっている場合
                        if($data['IsTwoFactor'] == 1){
                            // 2段階認証フラグを設定して2段階認証画面へ
                            SessionInsert('NeedTwoFactor', True);
                            SessionInsert('secret', $data['TwoFactorSecret']);
                            SessionInsert('IsAuth', False);
                            SessionInsert('UserId', $data['id']);

                            header('Location: /AuthSample/TwoFactor/whichTwoFactor.php');

                        // 2段階認証が無効になっている場合
                        }else {
                            // ログイン状態にしてマイページへ
                            SessionInsert('IsAuth', True);
                            SessionInsert('UserId', $data['id']);
                            header('Location: /AuthSample/mypage.php');
                        }
                    }else{
                        // メールアドレスもGoogleアカウントと一致するものが無かった場合はGoogleアカウントのメールアドレス、識別ID、ニックネームを使用して新規登録するページに遷移
                        SessionInsert('email', $payload['email']);
                        SessionInsert('userid', $payload['sub']);
                        SessionInsert('username', $payload['name']);

                        header('Location: /AuthSample/GAuthAdd.php');
                    }
                }
            }else{
                // 2段階認証が有効になっている場合
                if($data['IsTwoFactor'] == 1){
                    // 2段階認証フラグを設定して2段階認証画面へ
                    SessionInsert('NeedTwoFactor', True);
                    SessionInsert('secret', $data['TwoFactorSecret']);
                    SessionInsert('IsAuth', False);
                    SessionInsert('UserId', $data['id']);

                    header('Location: /AuthSample/TwoFactor/whichTwoFactor.php');

                    // 2段階認証が無効になっている場合
                }else {
                    // ログイン状態にしてマイページへ
                    SessionInsert('IsAuth', True);
                    SessionInsert('UserId', $data['id']);
                    header('Location: /AuthSample/mypage.php');
                }
            }
        }else{
            // SQLが正しく実行できなかった場合
            SessionInsert('err', 'エラーが発生しました。もう一度お試し下さい。');
            header('Location: /AuthSample/login.php');
        }
        // PDO接続解除
        $pdo = null;
    }else{
        // Googleとの認証が上手くいかなかった場合
        SessionInsert('err', 'エラーが発生しました。もう一度お試し下さい。');
        header('Location: /AuthSample/login.php');
    }
}else{
    // POST送信でなかった場合
    header('Location: /AuthSample/login.php');
}