<?php
include 'Tools/IsInGetTools.php';
include dirname(__FILE__).'/Tools/ValidateAndSecure.php';
SessionStarter();
if(isset($_SESSION['finished'])){
    if(!isset($_SESSION['twofactor'])) {
        $_SESSION = array();
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 3600, $params['path']);
        }
        session_destroy();
    }else{
        unset($_SESSION['finished']);
        unset($_SESSION['twofactor']);
    }
    $title = 'Finished';
    $card_name = '申請完了';
    $message = '';
    $errtype = False;

    $form = <<<EOF
<p>ご指定のメールアドレスにURLを送信致しました。<br>
24時間以内にメールに記載されたURLから手続きをお願い致します。
</p>
<div style="text-align:center;">
<button type="button" class="btn btn-primary" onclick="location.href='/AuthSample/mypage.php'" style="width: 90%;">戻る</button>
</div>
EOF;

    $option = '';


    $scriptTo = 'JavaScript/Login.js';

    include dirname(__FILE__).'/Template/BaseTemplate.php';
}else{
    header('Location: /AuthSample/login.php');
}
