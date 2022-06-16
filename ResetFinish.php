<?php
/*
 * 新規登録完了画面
 */
// 必要ファイルのインクルード
include 'Tools/IsInGetTools.php';
include 'Tools/ValidateAndSecure.php';

// セッション開始
SessionStarter();

// 登録完了フラグがセッションに存在する場合
if(isset($_SESSION["registration"])){
    // セッション解除処理（ログアウト）
    $_SESSION = array();
    if( ini_get( 'session.use_cookies' ) )
    {
        $params = session_get_cookie_params();
        setcookie( session_name(), '', time() - 3600, $params[ 'path' ] );
    }
    session_destroy();

    $title = 'Forget';
    $card_name = 'パスワードのリセット';
    $errtype = False;

    // フォーム作成
    $form = <<<EOF
<p>パスワードのリセットが完了しました。<br>
次回ログインからは新しいパスワードでログインできます。
</p>
<div style="text-align: center;">
    <button type="button" class="btn btn-primary" onclick="location.href='/AuthSample/login.php'" style='width: 90%'>ログイン</button>
</div>
EOF;

    // テンプレートファイル読み込み
    include dirname(__FILE__).'/Template/BaseTemplate.php';
}else{
    // 登録完了時ではない場合
    header('Location: /AuthSample/login.php');
}