<?php
/*
*　ユーザー名変更処理
*/
// 必要ファイルのインクルード
include dirname(__FILE__) . '/../Tools/Session.php';
include dirname(__FILE__) . '/../Tools/SQL.php';
include dirname(__FILE__) . '/../Tools/ValidateAndSecure.php';
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
    if(isset($_POST['password_old']) && isset($_POST['password1']) && isset($_POST['password2'])  && trim($_POST['password_old']) != '' && trim($_POST['password1']) != ''){
        // IDを基にUserテーブルからデータを取得
        $id = SessionReader('UserId');
        $stmt = $pdo->prepare('SELECT * FROM User WHERE id = :id');
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $res = $stmt->execute();

        // SQLが正しく実行できなかった場合
        if(!$res){
            SessionInsert('err', 'エラーが発生しました。もう一度お試し下さい。');
            header('Location: /'.$SERVICE_ROOT.'/Change/password.php');
            // SQLが正しく実行できた場合
        }else{
            // データ取得
            $get = $stmt->fetch();

            // データが存在する場合
            if(!is_bool($get)){
                // パスワードを確認
                if(password_verify($_POST['password_old'], $get['pass'])){
                    // パスワード1と2が一致しているか
                    if($_POST['password1'] == $_POST['password2']) {
                        // 現在と同じパスワードではないか
                        if(!password_verify($_POST['password1'], $get['pass'])) {
                            if(PasswordValid($_POST['password1'])) {
                                $password = password_hash($_POST['password1'], PASSWORD_DEFAULT);
                                // ユーザー名をアップデート
                                $stmt = $pdo->prepare('UPDATE User SET pass = :password WHERE id = :id');
                                $stmt->bindValue(':password', $password, PDO::PARAM_STR);
                                $stmt->bindValue(':id', $id, PDO::PARAM_INT);
                                $res = $stmt->execute();

                                // SQLが正しく実行できた場合
                                if ($res) {
                                    SessionInsert('registration', True);
                                    header('Location: /'.$SERVICE_ROOT.'/Auth/ResetFinish.php');
                                    // SQLが正しく実行できなかった場合
                                } else {
                                    SessionInsert('err', 'エラーが発生しました。もう一度お試し下さい。');
                                    header('Location: /'.$SERVICE_ROOT.'/Change/password.php');
                                }
                            }else{
                                SessionInsert('err', 'パスワードが条件を満たしていません。');
                                header('Location: /'.$SERVICE_ROOT.'/Change/password.php');
                            }
                        }else{
                            SessionInsert('err', '現在と同じパスワードには変更できません。');
                            header('Location: /'.$SERVICE_ROOT.'/Change/password.php');
                        }
                    }else{
                        SessionInsert('err', 'パスワードとパスワード(確認用)が一致しません。');
                        header('Location: /'.$SERVICE_ROOT.'/Change/password.php');
                    }
                // パスワードが間違っている場合
                }else{
                    SessionInsert('err', 'パスワードが間違っています。');
                    header('Location: /'.$SERVICE_ROOT.'/Change/password.php');
                }
                // データが存在しない場合
            }else{
                header('Location: /'.$SERVICE_ROOT.'/Process/Logout.php');
            }
        }
    }else{
        SessionInsert('err', 'ユーザー名またはパスワードが入力されていません。');
        header('Location: /'.$SERVICE_ROOT.'/Change/password.php');
    }
// POST送信ではない場合
}else{
    header('Location: /'.$SERVICE_ROOT.'/Auth/login.php');
}