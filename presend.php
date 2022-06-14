<?php
include 'Tools/IsInGetTools.php';
SessionStarter();
if(isset($_SESSION['finished'])){
    $_SESSION = array();
    if( ini_get( 'session.use_cookies' ) )
    {
        $params = session_get_cookie_params();
        setcookie( session_name(), '', time() - 3600, $params[ 'path' ] );
    }
    session_destroy();
    $title = 'Registration';
    $card_name = '新規登録';
    $message = '';
    $errtype = False;

    $form = <<<EOF
<p>ご指定のメールアドレスに本登録用メールアドレスを送信致しました。<br>
24時間以内にメールに記載されたURLから本登録をお願い致します。
</p>
EOF;

    $option = <<<EOF
<p>アカウントをお持ちではありませんか？<a href="#">新規登録</a></p>
<p>パスワードをお忘れですか？<a href="#">パスワードのリセット</a></p>
EOF;


    $scriptTo = 'JavaScript/Login.js';

    include dirname(__FILE__).'/Template/BaseTemplate.php';
}else{
    header('Location: /AuthSample/login.php');
}
