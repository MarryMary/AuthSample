<?php
/*
 * ログアウト処理をするファイル
 */
// 必要ファイルを代入
include dirname(__FILE__).'/../Tools/Session.php';

// セッションを開始・破棄してログイン画面に遷移
SessionStarter();
SessionUnset();
header('Location: /AuthSample/login.php');