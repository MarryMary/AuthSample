<?php
function SessionStarter() :void
{
    if ((function_exists('session_status') && session_status() !== PHP_SESSION_ACTIVE) || !session_id()) {
        session_start();
    }
}

function SessionReader($key)
{
    return $_SESSION[$key];
}

function SessionIsIn($key): bool
{
    return isset($_SESSION[$key]);
}

function SessionInsert($key, $value): void
{
    $_SESSION[$key] = $value;
}

function SessionUnset($key = ""): void
{
    if($key == ""){
        $_SESSION = array();
        if( ini_get( 'session.use_cookies' ) )
        {
            $params = session_get_cookie_params();
            setcookie( session_name(), '', time() - 3600, $params[ 'path' ] );
        }
        session_destroy();
    }else{
        unset($_SESSION[$key]);
    }
}