<?php
/*
 * 新規登録の最終確認画面を表示するファイル
 */
// 必要ファイルのインクルード
include dirname(__FILE__).'/../Tools/Session.php';
include dirname(__FILE__).'/../Tools/Uploader.php';
include dirname(__FILE__).'/../Tools/ValidateAndSecure.php';
include dirname(__FILE__).'/../Template/ServiceData.php';

// セッションの開始
SessionStarter();

// POST送信の場合
if($_SERVER['REQUEST_METHOD'] == 'POST'){
    // セッションにメールアドレスが代入されており、usernameとpassword1とpassword2が送信されている場合
    if(SessionIsIn('email') && isset($_POST['username'])&& isset($_POST['password1']) && isset($_POST['password2']) && trim($_POST['username']) != '' && trim($_POST['password1']) != ''){
        // password1と2が同じ場合
        if($_POST['password1'] == $_POST['password2']){
            // メールアドレスとパスワードとユーザー名がバリデーションに合格した場合
            if(EmailValid($_SESSION['email']) && PasswordValid($_POST['password1']) && UserNameValid($_POST['username'])){
                // パスワードとユーザー名もセッションに代入
                SessionInsert('password', $_POST['password1']);
                SessionInsert('username', $_POST['username']);

                // ファイルのアップロードが成功した場合
                if(isset($file)){
                    SessionInsert('filename', $file);
                    // 最終確認画面に遷移
                    header('Location: /'.$SERVICE_ROOT.'/Auth/RegCheck.php');
                // ファイルのアップロードに失敗した場合
                }else{
                    SessionInsert('err', 'ファイルのアップロードに失敗しました。');
                    header('Location: /'.$SERVICE_ROOT.'/Auth/MainRegistration.php?token='.SessionReader('token'));
                }
            // バリデーションに不合格だった場合
            }else{
                SessionInsert('err', 'メールアドレスまたはパスワード、ユーザー名のいずれかが条件に一致しません。');
                header('Location: /'.$SERVICE_ROOT.'/Auth/MainRegistration.php?token='.SessionReader('token'));
            }
        // password1と2が異なる場合
        }else{
            SessionInsert('err', 'パスワードが一致しません。');
            header('Location: /'.$SERVICE_ROOT.'/Auth/MainRegistration.php?token='.SessionReader('token'));
        }
    // メールアドレスとパスワード、ユーザー名のいずれかが入力されていなかった場合
    }else{
        SessionInsert('err', 'メールアドレスまたはパスワード、ユーザー名のいずれかが入力されていません。');
        header('Location: /'.$SERVICE_ROOT.'/Auth/MainRegistration.php?token='.SessionReader('token'));
    }
//POST送信ではない場合
}else{
    header('Location: /'.$SERVICE_ROOT.'/Auth/login.php');
}