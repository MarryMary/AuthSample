<?php
/*
* ログイン処理の実行ファイル
*/

//必要ファイルのインクルード
include dirname(__FILE__).'/../Tools/Session.php';
include dirname(__FILE__).'/../Tools/SQL.php';

//セッション開始
SessionStarter();

// POST送信のみ受け付ける
if($_SERVER['REQUEST_METHOD'] == 'POST'){
    // emailとpasswordフィールドに送信されているか、または空文字でないか
    if(isset($_POST['email']) && isset($_POST['password']) && trim($_POST['email']) != '' && trim($_POST['password']) !=  ''){
        // 論理削除から30日が経過したユーザーを物理削除
        $stmt = $pdo->prepare("delete from User WHERE delete_flag = 1 AND delete_at<=sysdate() - interval 30 day");
        $stmt->execute();
        
        // メールアドレスを基にデータを取得
        $stmt = $pdo->prepare("SELECT * FROM User WHERE email = :email");
        $stmt->bindParam( ':email', $_POST['email'], PDO::PARAM_STR);
        $res = $stmt->execute();

        // SQLが実行できた場合
        if($res) {
            // データを全件取得
            $data = $stmt->fetch();
            
            // もしデータが全県取得できていて、パスワードが一致する場合
            is_bool($data) ? '' : $password = $data['pass'];
            if(!is_bool($data) && password_verify($_POST['password'], $password)){
                
                // 論理削除状態でない場合
                if($data['delete_flag'] == 1){
                    // 2段階認証が有効化されている場合
                    if($data['IsTwoFactor'] == 1){
                        // 2段階認証フラグとユーザーIDをセッションに代入、まだ認証されていないことをセッションに代入
                        SessionInsert('IsAuth', False);
                        SessionInsert('UserId', $data['id']);
                        SessionInsert('NeedTwoFactor', True);

                        // 2段階認証のメニューを表示
                        header('Location: /AuthSample/TwoFactor/whichTwoFactor.php');
                    }else{
                        // 2段階認証が有効化されていない場合はマイページにリダイレクト
                        SessionInsert('IsAuth', True);
                        SessionInsert('UserId', $data['id']);
                        header('Location: /AuthSample/mypage.php');
                    }
                }else{
                    // 論理削除状態の場合
                    SessionInsert('Recover', True);
                    SessionInsert('IsAuth', False);
                    SessionInsert('UserId', $data['id']); 
                    header('Location: /AuthSample/RecoverAccount.php');
                }
            }else{
                // ユーザー情報が見つからなかった場合
                SessionInsert('err', 'メールアドレスまたはパスワードが間違っています。');
                header('Location: /AuthSample/login.php');
            }
        }else{
            // SQLが正しく実行できなかった場合
            SessionInsert('err', 'エラーが発生しました。もう一度お試し下さい。');
            header('Location: /AuthSample/login.php');
        }
        // PDO接続解除
        $pdo = null;
    }else{
        // メールアドレスまたはパスワードが入力されていなかった場合
        SessionInsert('err', 'メールアドレスまたはパスワードが入力されていません。');
        header('Location: /AuthSample/login.php');
    }
}else{
    // POST送信でなかった場合
    header('Location: /AuthSample/login.php');
}