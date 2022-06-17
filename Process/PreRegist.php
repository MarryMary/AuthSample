<?php
/*
 * 仮ユーザー登録メールを送信するファイル
 */
// 必要ファイルのインクルード
include dirname(__FILE__).'/../Tools/Session.php';
include dirname(__FILE__).'/../Tools/MailSender.php';
include dirname(__FILE__).'/../Tools/SQL.php';
include dirname(__FILE__).'/../Tools/UUID.php';
include dirname(__FILE__).'/../Template/ServiceData.php';


// セッションの開始
SessionStarter();

// POST送信されているかどうか
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // メールアドレスが入力されているかどうか
    if (isset($_POST['email']) && trim($_POST['email']) != '') {
        // 登録タイプを0(新規登録)に
        $type = 0;

        // ユーザーテーブルと仮ユーザーテーブルをメールアドレスを基に検索
        $prestmt = $pdo->prepare("SELECT * FROM User WHERE email = :email");
        $prestmt->bindParam(':email', $_POST['email'], PDO::PARAM_STR);
        $cr = $prestmt->execute();

        $stmt = $pdo->prepare("SELECT * FROM PreUser WHERE email = :email");
        $stmt->bindParam(':email', $_POST['email'], PDO::PARAM_STR);
        $precr = $stmt->execute();

        // 両方のSQLが正しく実行できた場合
        if($cr && $precr){
            // どちらのテーブルにもデータが存在しない場合
            if(is_bool($prestmt->fetch()) && is_bool($stmt->fetch())){
                // 仮ユーザーテーブルに情報をインサート
                $stmt = $pdo->prepare("INSERT INTO PreUser (email, user_token, register_type) VALUES (:email, :user_token, :register_type)");
                $stmt->bindParam(':email', $_POST['email'], PDO::PARAM_STR);
                $stmt->bindParam(':user_token', $uuid, PDO::PARAM_STR);
                $stmt->bindParam(':register_type', $type, PDO::PARAM_INT);
                $res = $stmt->execute();

                // SQLが正しく実行できた場合
                if ($res) {
                    // テンプレートを取得してメールを送信
                    $template = file_get_contents(dirname(__FILE__).'/../Template/mainregist.html');
                    $template = str_replace('{{SERVICENAME}}', $SERVICE_NAME, $template);
                    $template = str_replace('{{URL}}', $SERVICE_URL.'MainRegistration.php?token='.$uuid, $template);
                    EmailSender($_POST['email'], '本登録のご案内', $template);

                    // 登録完了フラグを立てて送信完了画面に遷移
                    SessionInsert('finished', True);
                    header('Location: /AuthSample/presend.php');
                // SQLが正しく実行できなかった場合
                } else {
                    SessionInsert('err', 'エラーが発生しました。もう一度お試し下さい。');
                    header('Location: /AuthSample/register_pre.php');
                }
                // PDO接続解除
                $pdo = null;
            // ユーザーテーブルまたは仮ユーザーテーブルにデータが存在していた場合
            }else{
                SessionInsert('err', 'そのメールアドレスは既に登録されています。');
                header('Location: /AuthSample/register_pre.php');
            }
        // SQLが正しく実行できなかった場合
        }else{
            SessionInsert('err', 'エラーが発生しました。もう一度お試し下さい。');
            header('Location: /AuthSample/register_pre.php');
        }
    // メールアドレスが入力されていなかった場合
    } else {
        $_SESSION['err'] = 'メールアドレスが入力されていません。';
        header('Location: /AuthSample/register_pre.php');
    }
// POST送信ではない場合
} else {
    header('Location: /AuthSample/login.php');
}