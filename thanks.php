<?php
/*
 * アカウント削除完了画面
 */
//必要ファイルのインクルード
include 'Tools/Session.php';
include 'Tools/ValidateAndSecure.php';

// セッションの開始
SessionStarter();

// アカウント削除完了フラグがセッションに存在する場合
if(SessionIsIn('finished')){
    // ログアウト
    SessionUnset();

    $title = 'Thank you for using';
    $card_name = 'アカウント削除完了';
    $errtype = False;

    // フォーム作成
    $form = <<<EOF
<p>
    アカウントの削除が完了しました。<br>
    ご利用ありがとうございました。<br>
    また、本日の削除から30日以内に再ログインをして頂くと、アカウントを復旧して再度御利用頂くことが可能です。
</p>
<div style="text-align:center;">
    <button type="button" class="btn btn-primary" onclick="location.href='/AuthSample/index.php'" style="width: 90%;">ホームへ戻る</button>
</div>
EOF;

    // テンプレートファイル読み込み
    include dirname(__FILE__).'/Template/BaseTemplate.php';
}else{
    // メール送信後でなければログイン画面に推移
    header('Location: /AuthSample/login.php');
}
