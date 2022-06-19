<?php
/*
 * メール更新用のメールを送信する処理のファイル
 */
// 必要ファイルのインクルード
include dirname(__FILE__).'/../Tools/Session.php';
include dirname(__FILE__).'/../Tools/MailSender.php';
include dirname(__FILE__).'/../Tools/SQL.php';
include dirname(__FILE__).'/../Tools/UUID.php';
include dirname(__FILE__).'/../Template/ServiceData.php';

// セッション開始
SessionStarter();

// POST送信の場合
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // メールアドレスが入力されている場合
    if (isset($_POST['email']) && trim($_POST['email']) != '' && isset($_POST['password']) && trim($_POST['password']) != '') {
        // 登録タイプを2に（メールアドレス更新）
        $type = 2;
        // ユーザーテーブルと仮ユーザーテーブルからメールアドレスを基準にデータを検索
        $mainstmt = $pdo->prepare("SELECT * FROM User WHERE email = :email");
        $mainstmt->bindParam(':email', $_POST['email'], PDO::PARAM_STR);
        $cr = $mainstmt->execute();

        $stmt = $pdo->prepare("SELECT * FROM PreUser WHERE email = :email");
        $stmt->bindParam(':email', $_POST['email'], PDO::PARAM_STR);
        $precr = $stmt->execute();

        // 両方のSQLが正しく実行できた場合
        if($cr && $precr){

            // 現在のユーザーデータを取得
            $id = SessionReader('UserId');
            $state = $pdo->prepare("SELECT * FROM User WHERE id = :id");
            $state->bindParam(':id', $id, PDO::PARAM_STR);
            $state->execute();
            $data = $state->fetch();

            // ユーザーテーブルにも仮ユーザーテーブルにもデータがない場合
            if(is_bool($mainstmt->fetch()) && is_bool($stmt->fetch())){
                if(password_verify($_POST['password'], $data['pass'])){
                    // 仮ユーザーテーブルにデータをインサート
                    $stmt = $pdo->prepare("INSERT INTO PreUser (email, user_token, register_type) VALUES (:email, :user_token, :register_type)");
                    $stmt->bindParam(':email', $_POST['email'], PDO::PARAM_STR);
                    $stmt->bindParam(':user_token', $uuid, PDO::PARAM_STR);
                    $stmt->bindParam(':register_type', $type, PDO::PARAM_INT);
                    $res = $stmt->execute();

                    // SQLが正しく実行できた場合
                    if ($res) {
                        // メールテンプレートを取得して、サービス名とURLを設定してメール送信
                        $template = file_get_contents(dirname(__FILE__).'/../Template/MailUpdate.html');
                        $template = str_replace('{{URL}}', $SERVICE_URL.'MainEmailChange.php?token='.$uuid, $template);
                        EmailSender($_POST['email'], 'メールアドレス変更のご案内', $template);

                        // 登録完了フラグを立てて送信済み画面へ遷移
                        SessionInsert('finished', True);
                        SessionInsert('mailchange', True); 
                        header('Location: /AuthSample/presend.php');
                        // SQLが正しく実行できなかった場合
                    } else {
                        SessionInsert('err', 'エラーが発生しました。もう一度お試し下さい。');
                        header('Location: /AuthSample/forget.php');
                    }
                    // PDO接続解除
                    $pdo = null;
                }else{
                    SessionInsert('err', 'パスワードが間違っています。');
                    header('Location: /AuthSample/Change/email.php');
                }
                // ユーザーテーブルにデータが無いかまたは仮ユーザーテーブルにデータが入っている場合
            }else{
                SessionInsert('err', 'そのメールアドレスは他のアカウントに登録されているか、または既に貴方のアカウントに登録されている可能性があります。');
                header('Location: /AuthSample/Change/email.php');
            }
        }
        // メールアドレスが入力されていない場合
    } else {
        SessionInsert('err', 'メールアドレスまたはパスワードが入力されていません。');
        header('Location: /AuthSample/Change/email.php');
    }
// POST送信ではない場合
} else {
    header('Location: /AuthSample/login.php');
}