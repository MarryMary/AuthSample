<?php
/*
 * パスワード忘れで新規パスワード登録処理を行うファイル
 */
// 必要ファイルのインクルード
include dirname(__FILE__).'/../Tools/Session.php';
include dirname(__FILE__).'/../Tools/SQL.php';
include dirname(__FILE__).'/../Tools/ValidateAndSecure.php';
include dirname(__FILE__).'/../Template/ServiceData.php';

// セッションの開始
SessionStarter();

// POST送信かどうか
if($_SERVER['REQUEST_METHOD'] == 'POST'){
    // セッションに代入されているトークンを変数に代入
    $token = SessionReader('token');
    // password1と2が送信されているか
    if(isset($_POST['password1']) && isset($_POST['password2']) && trim($_POST['password1']) != ''){
        // password1と2が同じかどうか
        if($_POST['password1'] == $_POST['password2']){
            // パスワードが条件に一致しているかどうか
            if(PasswordValid($_POST['password1'])){
                // パスワードをハッシュ化
                $password = password_hash($_POST['password1'], PASSWORD_DEFAULT);

                // 仮ユーザー登録テーブルをトークンで検索してデータを取得
                $stmt = $pdo->prepare("SELECT * FROM PreUser WHERE user_token = :token");
                $stmt->bindParam(':token', $token, PDO::PARAM_STR);
                $stmt->execute();
                $data = $stmt->fetch();

                // 仮ユーザーテーブルに登録されているメールアドレスからユーザーテーブルを検索
                $stmt = $pdo->prepare("SELECT * FROM User WHERE email = :email");
                $stmt->bindParam(':email', $data['email'], PDO::PARAM_STR);
                $stmt->execute();
                $get = $stmt->fetch();

                // 現在ユーザーテーブルに登録されているパスワードと一致していないかどうか
                if(!password_verify($_POST["password1"], $get['pass'])) {
                    // ユーザーテーブルのパスワードを新しいパスワードで上書き
                    $stmt = $pdo->prepare("UPDATE User SET pass = :password WHERE id = :id");
                    $stmt->bindParam(':password', $password, PDO::PARAM_STR);
                    $stmt->bindParam(':id', $get['id'], PDO::PARAM_INT);
                    $res = $stmt->execute();

                    // SQLが正しく実行できた場合
                    if ($res) {
                        // 仮ユーザーテーブルから今回の情報を削除
                        $stmt = $pdo->prepare("DELETE FROM PreUser WHERE user_token = :user_token");
                        $stmt->bindParam(':user_token', $token, PDO::PARAM_STR);
                        $res = $stmt->execute();

                        // 登録完了フラグを立ててリセット完了画面へ遷移
                        SessionInsert('registration', True);
                        header('Location: /'.$SERVICE_ROOT.'/Auth/ResetFinish.php');
                    // SQLが正しく実行できなかった場合
                    } else {
                        header('Location: /'.$SERVICE_ROOT.'/Auth/login.php');
                    }
                // 現在のパスワードと同じパスワードが送信された場合
                }else{
                    SessionInsert('err', '現在のパスワードと同じパスワードを設定することはできません。');
                    header('Location: /'.$SERVICE_ROOT.'/Auth/MainPasswordReset.php?token='.$token);
                }
            // パスワードのバリデーションに引っかかった場合
            }else{
                SessionInsert('err', 'パスワードが条件に一致しません。');
                header('Location: /'.$SERVICE_ROOT.'/Auth/MainPasswordReset.php?token='.$token);
            }
        // password1と2が一致しない場合
        }else{
            SessionInsert('err', 'パスワードが一致しません。');
            header('Location: /'.$SERVICE_ROOT.'/Auth/MainPasswordReset.php?token='.$token);
        }
    // メールアドレスまたはパスワードが未入力の場合
    }else{
        SessionInsert('err', 'メールアドレスまたはパスワードが入力されていません。');
        header('Location: /'.$SERVICE_ROOT.'/Auth/MainPasswordReset.php?token='.$token);
    }
// POST送信ではない場合
}else{
    header('Location: /'.$SERVICE_ROOT.'/Auth/login.php');
}