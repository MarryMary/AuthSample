<?php
/*
 * アカウント削除処理を実行するファイル
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
    // パスワードが入力されており、削除時のデータ削除に同意している場合
    if (isset($_POST['understand']) && isset($_POST['password']) && trim($_POST['understand']) != '') {
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
                $now = date('Y-m-d H:i:s');
                $id = SessionReader('UserId');
                // 仮ユーザーテーブルにデータをインサート
                $stmt = $pdo->prepare("UPDATE User SET delete_flag = 1, delete_at = :today WHERE id = :id");
                $stmt->bindParam(':delete_at', $now, PDO::PARAM_STR);
                $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                $res = $stmt->execute();

                // SQLが正しく実行できた場合
                if ($res) {
                    // 登録完了フラグを立てて削除完了画面へ遷移
                    SessionInsert('finished', True);
                    header('Location: /AuthSample/thanks.php');
                // SQLが正しく実行できなかった場合
                } else {
                    SessionInsert('err', 'エラーが発生しました。もう一度お試し下さい。');
                    header('Location: /AuthSample/Change/del.php');
                }
                // PDO接続解除
                $pdo = null;
            // ユーザーテーブルにデータが無いか仮ユーザーテーブルにデータが入っている場合
            }else{
                SessionInsert('err', 'そのメールアドレスは現在仮登録中か、登録されていない可能性があります。');
                header('Location: /AuthSample/Change/del.php');
            }
        }
    // パスワードまたは確認チェックボックスにチェックが入っていなかった場合
    } else {
        if(!isset($_POST['understand'])){
            SessionInsert('err', '削除内容に同意頂けない場合はアカウント削除できません。');
        }else{
            SessionInsert('err', 'メールアドレスが入力されていません。');
        }
        header('Location: /AuthSample/Change/del.php');
    }
// POST送信ではない場合
} else {
    header('Location: /AuthSample/login.php');
}