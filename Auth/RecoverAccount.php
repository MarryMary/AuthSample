<?php
/*
 * アカウント復元確認画面
 */
//必要ファイルのインクルード
include dirname(__FILE__).'/../Tools/Session.php';
include dirname(__FILE__).'/../Tools/ValidateAndSecure.php';
include dirname(__FILE__).'/../Template/ServiceData.php';

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
    <button type="button" class="btn btn-primary" onclick="location.href='/{$SERVICE_ROOT}/Process/RecoverAccount.php?cancel=true'" style="width: 40%;">キャンセル</button>
    <button type="button" class="btn btn-success" onclick="location.href='/{$SERVICE_ROOT}/Process/RecoverAccount.php'" style="width: 40%;">復元</button><br>
</div>
EOF;

    $JS = <<<EOF
<script>
    history.pushState(null, null, location.href);
    window.addEventListener('popstate', (e) => {
      history.go(1);
      alert('このサイトでは戻るボタンの使用することはできません。前の画面に戻る場合は必ずページ内のキャンセルボタンを押して下さい。');
    });
</script>
EOF;

    // テンプレートファイル読み込み
    include dirname(__FILE__).'/../Template/BaseTemplate.php';
}else{
    // メール送信後でなければログイン画面に推移
    header('Location: /'.$SERVICE_ROOT.'/Auth/login.php');
}
