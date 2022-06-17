<?php
/*
 * 新規登録完了画面
 */
// 必要ファイルのインクルード
include 'Tools/Session.php';
include 'Tools/ValidateAndSecure.php';

// セッション開始
SessionStarter();

// 登録完了フラグがセッションに存在する場合
if(SessionIsIn('registration')){
    // セッション解除処理（ログアウト）
    SessionUnset();

    $title = 'Forget';
    $card_name = 'パスワードのリセット';
    $errtype = False;

    // フォーム作成
    $form = <<<EOF
<p>パスワードの変更が完了しました。<br>
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