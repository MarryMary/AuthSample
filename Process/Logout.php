<?php
include dirname(__FILE__).'/../IsInGetTools.php';
SessionStarter();
$_SESSION = array();
if( ini_get( 'session.use_cookies' ) )
{
    $params = session_get_cookie_params();
    setcookie( session_name(), '', time() - 3600, $params[ 'path' ] );
}
session_destroy();
header('Location: /AuthSample/login.php');