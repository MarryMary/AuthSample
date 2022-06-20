<?php
/*
 * 通常登録時の最終確認画面
 */
// 必要ファイルのインクルード
include dirname(__FILE__).'/../Tools/Session.php';
include dirname(__FILE__).'/../Tools/ValidateAndSecure.php';
include dirname(__FILE__).'/../Template/ServiceData.php';

// セッション開始
SessionStarter();

// email、usernameがセッションにある場合
if(SessionIsIn('email') && SessionIsIn('username')){
    $title = 'Registration';
    $card_name = '新規登録';
    $message = '以下の内容で登録します。';
    $errtype = False;
    if(SessionIsIn('err')){
        $errtype = True;
        $message = SessionReader('err');
        SessionUnset('err');
    }

    $file = SessionReader('filename');

    $email = SessionReader('email');
    $username = SessionReader('username');
    $password = str_repeat("●", strlen(SessionReader('password')));
    $token = SessionReader('token');

    // フォーム作成
    $form = <<<EOF
    <form action="/{$SERVICE_ROOT}/Process/Registration.php" method="POST" enctype="multipart/form-data">
        <input type='email' name='email' class="form-control" placeholder='メールアドレス' style='margin-bottom: 3%;' value='{$email}' disabled>
        <input type='text' name='username' class="form-control" placeholder='ユーザー名' style='margin-bottom: 3%;' value='{$username}' disabled>
        <input type='password' name='password' class="form-control" placeholder='パスワード' style='margin-bottom: 3%;' value='{$password}'>
        <p>プロフィール画像</p>
        <div style="text-align: center">
            <img src='{$file}' alt='profile' width='200' height='200'>
        </div>
        <div style="text-align: center; margin-top: 10px;">
            <button type="button" class="btn btn-primary" onclick="location.href='/{$SERVICE_ROOT}/Auth/MainRegistration.php?token={$token}'" style="width: 40%;">戻る</button>
            <button type="submit" class="btn btn-success" style="width: 40%;">登録</button><br>
        </div>
    </form>

    EOF;

    // テンプレートファイル読み込み
    include dirname(__FILE__).'/../Template/BaseTemplate.php';
}else{
    header('Location: /'.$SERVICE_ROOT.'/Auth/login.php');
}