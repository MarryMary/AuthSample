<?php
/*
 * アカウント復元確認画面
 */
//必要ファイルのインクルード
include 'Tools/Session.php';
include 'Tools/ValidateAndSecure.php';

// セッションの開始
SessionStarter();

// アカウント削除完了フラグがセッションに存在する場合
if(SessionIsIn('Recover')){

    $title = 'Recover your account';
    $card_name = 'アカウントの復元';
    $errtype = False;

    // フォーム作成
    $form = <<<EOF
<p>
    お使いのアカウントは過去30日以内に削除されています。<br>
    もし誤ってログインした場合はキャンセルボタンを押して下さい。<br>
    もし以前使用していたアカウントを復元したい場合は、復元ボタンを押して下さい。
</p>
<div style="text-align: center; margin-top: 10px;">
            <button type="button" class="btn btn-primary" onclick="/AuthSample/Process/RecoverAccount.php?cancel=true" style="width: 40%;">キャンセル</button>
            <button type="button" class="btn btn-success" onclick="/AuthSample/Process/RecoverAccount.php" style="width: 40%;">復元</button><br>
        </div>
EOF;

    // テンプレートファイル読み込み
    include dirname(__FILE__).'/Template/BaseTemplate.php';
}else{
    // メール送信後でなければログイン画面に推移
    header('Location: /AuthSample/login.php');
}
