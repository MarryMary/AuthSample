<?php
include 'Tools/IsInGetTools.php';
include dirname(__FILE__).'/Tools/ValidateAndSecure.php';

SessionStarter();
if(isset($_SESSION["registration"])){
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
<p>登録が完了致しました。<br>
引き続きサービスを利用するにはログインして下さい。
</p>
<div style="text-align: center;">
    <button type="button" class="btn btn-primary" onclick="location.href='/AuthSample/login.php'" style='width: 90%'>ログイン</button>
</div>
EOF;

    $option = '';


    $scriptTo = 'JavaScript/Login.js';

    include dirname(__FILE__).'/Template/BaseTemplate.php';
}else{
    header('Location: /AuthSample/login.php');
}