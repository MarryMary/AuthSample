<?php
/*
 * 新規登録の本登録（メールアドレスの有効性が確認できた場合）
 */
// 必要ファイルのインクルード
include 'Tools/Session.php';
include 'Tools/ValidateAndSecure.php';
include 'Tools/SQL.php';

// セッション開始
SessionStarter();

// トークンが送信されている場合
if(isset($_GET["token"])){
    // 24時間以上経過しているデータを物理削除
    $stmt = $pdo->prepare("delete from PreUser WHERE register_at<=sysdate() - interval 1 day");
    $stmt->execute();

    // そのトークンを持ったアカウントを検索(UUID V4)
    $stmt = $pdo->prepare("SELECT * FROM PreUser WHERE user_token = :token");
    $stmt->bindValue(':token', $_GET["token"], PDO::PARAM_STR);
    $res = $stmt->execute();

    // 正常にSQLが実行できた場合
    if($res){
        // 1件取得
        $result = $stmt->fetch();
        //取得できた場合（条件一致が0件の場合はFalseになる）
        if(!is_bool($result)){
            // セッションにトークン情報とメールアドレスを代入
            SessionInsert('token', $_GET['token']);
            SessionInsert('email', $result['email']);
            // email変数にemail情報を代入
            $email = $result['email'];


            $title = 'Registration';
            $card_name = '新規登録';
            $message = 'ログインを行う前に以下の情報を追加して下さい。';
            $errtype = False;
            if(SessionIsIn('err')){
                $errtype = True;
                $message = SessionReader('err');
                SessionUnset('err');
            }

            // cropper.js関連のCSSを読み込み
            $GAuthJS = <<<EOF
<link href="//cdnjs.cloudflare.com/ajax/libs/cropper/3.1.6/cropper.min.css" rel="stylesheet">
<script src="//cdnjs.cloudflare.com/ajax/libs/cropper/3.1.6/cropper.min.js"></script>
EOF;

            // フォーム作成
            $form = <<<EOF
<form action="Process/RegCheck.php" method="POST" enctype="multipart/form-data">
    <input type='email' name='email' class="form-control" placeholder='メールアドレス' style='margin-bottom: 3%;' value='{$result['email']}' disabled>
    <div class="mb-3">
        <input type='text' name='username' class="form-control" placeholder='ユーザー名'>
        <div id="emailHelp" class="form-text">ユーザー名は空白を除く1字以上50字以下である必要があります。</div>
    </div>
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


            // JavaScript指定
            $scriptTo = 'JavaScript/Register.js';
            // cropper.js関連のJavaScriptを読み込み
            $JS = '<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.12/cropper.js" type="text/javascript"></script>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-cropper/1.0.1/jquery-cropper.js" type="text/javascript"></script>';

            // テンプレートファイルをインクルード
            include dirname(__FILE__).'/Template/BaseTemplate.php';
        }else{
            //データベースに情報がなかった場合
            header('Location: /AuthSample/login.php');
        }
    }else{
        // 正常にデータベースへのSQLが実行できなかった場合
        header('Location: /AuthSample/login.php');
    }
}else{
    //トークンが送信されていなかった場合
    header('Location: /AuthSample/login.php');
}