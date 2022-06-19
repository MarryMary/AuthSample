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
    if (isset($_POST['understand']) && isset($_POST['password']) && trim($_POST['password']) != '') {
        // 登録タイプを1に（パスワード忘れ）
        $type = 1;
        $id = SessionReader('UserId');
        // ユーザーテーブルからIDを基準にデータを検索
        $mainstmt = $pdo->prepare("SELECT * FROM User WHERE id = :id");
        $mainstmt->bindParam(':id', $id, PDO::PARAM_INT);
        $cr = $mainstmt->execute();

        // SQLが正しく実行できた場合
        if($cr){
            $data = $mainstmt->fetch();
            // ユーザーテーブルにデータがあり、パスワードが正しい場合
            if(!is_bool($data) && password_verify($_POST['password'], $data['pass'])){
                $now = date('Y-m-d H:i:s');
                // 仮ユーザーテーブルにデータをインサート
                $stmt = $pdo->prepare("UPDATE User SET delete_flag = 1, delete_at = :today WHERE id = :id");
                $stmt->bindParam(':today', $now, PDO::PARAM_STR);
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
            }else{
                if(is_bool($data)){
                    var_dump($data);
                    exit;
                    header('Location: /AuthSample/Process/Logout.php'); 
                }else{
                    SessionInsert('err', 'パスワードが間違っています。');
                    header('Location: /AuthSample/Change/del.php'); 
                }
            }
        }else{
            // SQLが正しく実行できなかった場合
            SessionInsert('err', 'エラーが発生しました。もう一度お試し下さい。');
            header('Location: /AuthSample/Change/del.php');
        }
    // パスワードまたは確認チェックボックスにチェックが入っていなかった場合
    } else {
        if(!isset($_POST['understand'])){
            SessionInsert('err', '削除内容に同意頂けない場合はアカウント削除できません。');
        }else{
            SessionInsert('err', 'パスワードが入力されていません。');
        }
        header('Location: /AuthSample/Change/del.php');
    }
}