<?php
/*
 * Google シングル・サインオンで新規登録を行った場合の最終確認（この内容で登録するか確認する）画面
 */

// 必要モジュールのインクルード
include dirname(__FILE__).'/Tools/IsInGetTools.php';
include dirname(__FILE__).'/Tools/ValidateAndSecure.php';

// セッション開始
SessionStarter();
// もしセッションにemailとuseridが入っている場合
if(isset($_SESSION['email']) && isset($_SESSION['userid'])){
    $title = 'Registration';
    $card_name = '新規登録';
    $message = '以下の内容で登録します。';
    $errtype = False;
    if(array_key_exists('err', $_SESSION)){
        $errtype = True;
        $message = $_SESSION['err'];
        unset($_SESSION['err']);
    }

    $file = $_SESSION['filename'];

    $email = $_SESSION['email'];
    $username = $_SESSION['username'];

    //パスワードは文字数分黒丸を表示する(html編集対策)
    $password = str_repeat("●", strlen($_SESSION['password']));

    //form作成
    $form = <<<EOF
    <form action="Process/GAuthAdd.php" method="POST" enctype="multipart/form-data">
        <input type='email' name='email' class="form-control" placeholder='メールアドレス' style='margin-bottom: 3%;' value='{$email}' disabled>
        <input type='text' name='username' class="form-control" placeholder='ユーザー名' style='margin-bottom: 3%;' value='{$username}' disabled>
        <input type='password' name='password' class="form-control" placeholder='パスワード' style='margin-bottom: 3%;' value='{$password}'>
        <p>プロフィール画像</p>
        <div style="text-align: center">
            <img src='{$file}' alt='profile' width='200' height='200'>
        </div>
        <div style="text-align: center; margin-top: 10px;">
            <button type="button" class="btn btn-primary" onclick="history.back()" style="width: 40%;">戻る</button>
            <button type="submit" class="btn btn-success" style="width: 40%;">登録</button><br>
        </div>
    </form>

    EOF;

    // JavaScript指定
    $scriptTo = 'JavaScript/Register.js';

    // テンプレートファイル読み込み
    include dirname(__FILE__).'/Template/BaseTemplate.php';
}else{
    // 条件に一致しない場合はログイン画面に遷移
    header('Location: /AuthSample/login.php');
}