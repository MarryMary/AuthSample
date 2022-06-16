<?php
/*
 * Google シングル・サインオン時に基本情報がなかった場合（一度もこのサービスを利用したことがない場合）に表示される画面
 */

//必要ファイルのインクルード
include dirname(__FILE__).'/Tools/IsInGetTools.php';
include dirname(__FILE__).'/Tools/ValidateAndSecure.php';

// セッション開始(IsInGetTools.php内関数)
SessionStarter();

//セッションにemail、userid、user名が入っている場合の処理
if(isset($_SESSION['email']) && isset($_SESSION['userid']) && isset($_SESSION['username'])){
    // ページのタイトル
    $title = 'Registration';
    // ログインのカード（白い部分）の一番上に表示するタイトル
    $card_name = '新規登録';
    // メッセージ
    $message = 'ログインを行う前に以下の情報を追加して下さい。';
    // エラーがあるかどうか
    $errtype = False;
    // もしセッションにエラーが入っている場合は
    if(isset($_SESSION["err"])){
        // エラーがある
        $errtype = True;
        // エラー内容
        $message = $_SESSION['err'];
        // エラー削除
        unset($_SESSION['err']);
    }

    // メール（GMail）
    $email = $_SESSION['email'];
    //ユーザー名(Google)
    $username = $_SESSION['username'];

    //cropper.js用のCSSを読み込み
    $GAuthJS = <<<EOF
<link href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.12/cropper.css" rel="stylesheet" type="text/css">
EOF;

    // フォーム作成
    $form = <<<EOF
    <form action="Process/GCheck.php" method="POST" enctype="multipart/form-data">
        <input type='email' name='email' class="form-control" placeholder='メールアドレス' style='margin-bottom: 3%;' value='{$email}' disabled>
        <input type='text' name='username' class="form-control" placeholder='ユーザー名' style='margin-bottom: 3%;' value='{$username}' disabled>
        <div class="mb-3">
            <input type='password' name='password1' class="form-control" placeholder='パスワード'>
            <div id="emailHelp" class="form-text">パスワードは8字以上16字以下で、「?、!、#、,」のいずれかの記号が入っている必要があります。</div>
        </div>
        <input type='password' name='password2' class="form-control" placeholder='パスワード(確認用)' style='margin-bottom: 3%;'>
        <p>プロフィール画像</p>
        <input type="file" name="UserPict" id="UserImage">
        <img id="selectImage" style="max-width:500px;">
        <input type="hidden" id="imageX" name="UserImageX" value="0"/>
        <input type="hidden" id="imageY" name="UserImageY" value="0"/>
        <input type="hidden" id="imageW" name="UserImageW" value="0"/>
        <input type="hidden" id="imageH" name="UserImageH" value="0"/>
        <div style="text-align: center; margin-top: 10px;">
            <button type='submit' class='btn btn-primary' style="width: 80%;">次へ</button>
        </div>
    </form>

    EOF;

    // JavaScriptファイルを指定
    $scriptTo = 'JavaScript/Register.js';
    //cropper.js関連のJavaScriptファイルを指定
    $JS = '<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.12/cropper.js" type="text/javascript"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-cropper/1.0.1/jquery-cropper.js" type="text/javascript"></script>';

    // テンプレートファイルをインクルード
    include dirname(__FILE__).'/Template/BaseTemplate.php';
}else{
    // もし上の条件に反していた場合はログイン画面に遷移して終了
    header('Location: /AuthSample/login.php');
}