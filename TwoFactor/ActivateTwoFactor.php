<?php
/*
 * 2段階認証を有効化するためのメールを送信するファイルです。
 */
// 必要ファイルのインクルード
include dirname(__FILE__).'/../Tools/Session.php';
include dirname(__FILE__).'/../Tools/MailSender.php';
include dirname(__FILE__) . '/../Tools/UUID.php';
include dirname(__FILE__).'/../Tools/SQL.php';
include dirname(__FILE__).'/../Template/ServiceData.php';

// セッション開始
SessionStarter();

// ログイン状態かどうか
if(SessionIsIn('IsAuth') && is_bool(SessionReader('IsAuth')) && SessionReader('IsAuth')){
    // ユーザーテーブルをidを基に検索
    $stmt = $pdo->prepare("SELECT * FROM User WHERE id = :id");
    $stmt->bindValue(':id',$_SESSION["UserId"], PDO::PARAM_INT);
    $res = $stmt->execute();

    // SQLが正しく実行できた場合
    if($res){
        // データを取得
        $data = $stmt->fetch();
        // データが存在する場合
        if(!is_bool($data)){
            // 2段階認証フラグが0(未設定)の場合
            if($data['IsTwoFactor'] == 0){
                // テンプレートを取得・URLをバインドしてメールを送信
                $url = $SERVICE_URL.'TwoFactor/EnableTwoFactor.php?token='.$uuid;
                $template = file_get_contents(dirname(__FILE__).'/../Template/TwoFactorEnable.html');
                $template = str_replace('{{URL}}', $url, $template);
                EmailSender($data['email'], '2段階認証有効化のご案内', $template);

                // 仮登録テーブルに登録タイプを3(2段階認証有効化)で登録
                $stmt = $pdo->prepare("INSERT INTO PreUser (user_token, email, register_type) VALUES (:token, :email, :type)");
                $stmt->bindValue(':token', $uuid, PDO::PARAM_STR);
                $stmt->bindValue(':email', $data['email'], PDO::PARAM_STR);
                $stmt->bindValue(':type', 3, PDO::PARAM_INT);
                $stmt->execute();

                // セッションに２段階認証設定フラグと完了フラグを立てて送信完了画面に遷移
                SessionInsert('finished', True);
                SessionInsert('twofactor', True);
                header("Location: /AuthSample/presend.php");
            // 2段階認証が既に設定済みの場合
            }else{
                header("Location: /AuthSample/mypage.php");
            }
        // データが存在しなかった場合
        }else{
            header("Location: /AuthSample/login.php");
        }
    // SQLが正しく実行できなかった場合
    }else{
        $_SESSION["err"] = "問題が発生しました。";
        header("Location: /AuthSample/TwoFactorAuthorize.php");
    }
// ログイン状態ではない場合
}else{
    header("Location: /AuthSample/login.php");
}