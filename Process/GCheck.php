<?php
/*
 * Googleアカウントによる新規登録時の登録内容確認画面表示前の処理ファイル
 */
// 必要ファイルのインクルード
include dirname(__FILE__).'/../Tools/Session.php';
include dirname(__FILE__).'/../Tools/Uploader.php';
include dirname(__FILE__).'/../Tools/ValidateAndSecure.php';

// セッション開始
SessionStarter();

// POST送信の場合
if($_SERVER['REQUEST_METHOD'] == 'POST'){
    // セッションにメールアドレスが入っており、パスワードとパスワード（確認用）が送信されているか
    if(SessionIsIn('email') && isset($_POST['password1']) && isset($_POST['password2']) && trim($_POST['password1']) != ''){
        // パスワードとパスワード（確認用）が正しいか
        if($_POST['password1'] == $_POST['password2']){
            // メールアドレスとパスワードが指定通りになっているか（バリデーション）
            if(EmailValid($_SESSION['email']) && PasswordValid($_POST['password1'])){
                // セッションにパスワードを代入
                SessionInsert('password', $_POST['password1']);
                // ファイルがアップロードされていて、ファイル変数がある場合
                if(isset($file)){
                    // ファイル名をセッションに代入し、最終チェック画面に遷移
                    SessionInsert('filename', $file);
                    header('Location: /AuthSample/GAuthAddCheck.php');
                // ファイルのアップロードに失敗している場合
                }else{
                    // ファイルのアップロードに失敗した旨をセッションに代入して登録画面に戻す
                    SessionInsert('err', "ファイルのアップロードに失敗しました。");
                    header('Location: /AuthSample/GAuthAdd.php');
                }
            // メールアドレスとパスワードがバリデーションで引っかかった場合
            }else{
                SessionInsert('err', 'メールアドレスまたはパスワードが条件に一致しません。');
                header('Location: /AuthSample/GAuthAdd.php');
            }
        // パスワード1と2が合わない場合
        }else{
            SessionInsert('err', 'パスワードが一致しません。');
            header('Location: /AuthSample/GAuthAdd.php');
        }
     // そもそも入力されていない場合
    }else{
        SessionInsert('err', 'メールアドレスまたはパスワードが入力されていません。');
        header('Location: /AuthSample/GAuthAdd.php');
    }
}else{
    header('Location: /AuthSample/GAuthAdd.php');
}