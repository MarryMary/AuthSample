<?php
/*
 * セッション系の関数を保有しているファイルです。
 * セッションを保存する手段がPHPの関数でなくても対応できるようにするための措置です。
 */

// セッションを開始する関数
function SessionStarter() :void
{
    // もしセッションが開始していなければセッションを開始します。
    if ((function_exists('session_status') && session_status() !== PHP_SESSION_ACTIVE) || !session_id()) {
        session_start();
    }
}

// セッションを読み取る関数
function SessionReader($key)
{
    return $_SESSION[$key];
}

// セッションの存在を確認する関数
function SessionIsIn($key): bool
{
    return isset($_SESSION[$key]);
}

// セッションを代入する関数
function SessionInsert($key, $value): void
{
    $_SESSION[$key] = $value;
}

//セッションを破棄する関数
function SessionUnset($key = ""): void
{
    // キーが空（初期値）であればクッキー含め全削除
    if($key == ""){
        $_SESSION = array();
        if( ini_get( 'session.use_cookies' ) )
        {
            $params = session_get_cookie_params();
            setcookie( session_name(), '', time() - 3600, $params[ 'path' ] );
        }
        session_destroy();
    // キーが指定されている場合はそのキーのセッションのみ破棄
    }else{
        unset($_SESSION[$key]);
    }
}