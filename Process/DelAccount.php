<?php
/*
 * パスワードリセットのメールを送信する処理のファイル
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
    if (isset($_POST['email']) && trim($_POST['email']) != '') {
        // 登録タイプを1に（パスワード忘れ）
        $type = 1;
        // ユーザーテーブルと仮ユーザーテーブルからメールアドレスを基準にデータを検索
        $mainstmt = $pdo->prepare("SELECT * FROM User WHERE email = :email");
        $mainstmt->bindParam(':email', $_POST['email'], PDO::PARAM_STR);
        $cr = $mainstmt->execute();

        // SQLが正しく実行できた場合
        if($cr){
            // ユーザーテーブルにデータがある場合
            if(!is_bool($mainstmt->fetch())){
                // 仮ユーザーテーブルにデータをインサート
                $stmt = $pdo->prepare("UPDATE User SET ");
                $stmt->bindParam(':email', $_POST['email'], PDO::PARAM_STR);
                $stmt->bindParam(':user_token', $uuid, PDO::PARAM_STR);
                $stmt->bindParam(':register_type', $type, PDO::PARAM_INT);
                $res = $stmt->execute();

                // SQLが正しく実行できた場合
                if ($res) {
                    // メールテンプレートを取得して、サービス名とURLを設定してメール送信
                    $template = file_get_contents(dirname(__FILE__).'/../Template/forget.html');
                    $template = str_replace('{{SERVICENAME}}', $SERVICE_NAME, $template);
                    $template = str_replace('{{URL}}', $SERVICE_URL.'MainPasswordReset.php?token='.$uuid, $template);
                    EmailSender($_POST['email'], 'パスワードリセットのご案内', $template);

                    // 登録完了フラグを立てて送信済み画面へ遷移
                    SessionInsert('finished', True);
                    header('Location: /AuthSample/presend.php');
                // SQLが正しく実行できなかった場合
                } else {
                    SessionInsert('err', 'エラーが発生しました。もう一度お試し下さい。');
                    header('Location: /AuthSample/forget.php');
                }
                // PDO接続解除
                $pdo = null;
            // ユーザーテーブルにデータが無いか仮ユーザーテーブルにデータが入っている場合
            }else{
                SessionInsert('err', 'そのメールアドレスは現在仮登録中か、登録されていない可能性があります。');
                header('Location: /AuthSample/forget.php');
            }
        }
    // メールアドレスが入力されていない場合
    } else {
        SessionInsert('err', 'メールアドレスが入力されていません。');
        header('Location: /AuthSample/forget.php');
    }
// POST送信ではない場合
} else {
    header('Location: /AuthSample/login.php');
}