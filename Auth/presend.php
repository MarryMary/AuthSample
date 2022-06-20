<?php
/*
 * メール送信後画面（全機能共通）
 */
//必要ファイルのインクルード
include dirname(__FILE__).'/../Tools/Session.php';
include dirname(__FILE__).'/../Tools/ValidateAndSecure.php';
include dirname(__FILE__).'/../Template/ServiceData.php';

// セッションの開始
SessionStarter();

// メール送信完了フラグがセッションに存在する場合
if(SessionIsIn('finished')){
    // それが2段階認証ではなかった場合（2段階認証の場合はログイン状態で全て処理を完結させる必要があるため）
    if(!SessionIsIn('twofactor') && !SessionIsIn('mailchange')) {
        SessionUnset();
    }else{
        // それぞれのセッション情報を削除(再度この画面へのアクセスを防ぐため)
        SessionUnset('finished');
        // 2段階認証設定フラグがある場合はそれも削除
        if(SessionIsIn('twofactor')) {
            SessionUnset('twofactor');
        // メール設定フラグがある場合はそれも削除
        }elseif(SessionIsIn('mailchange')){
            SessionUnset('mailchange'); 
        }
    }

    

    $title = 'Finished';
    $card_name = '申請完了';
    $errtype = False;

    // フォーム作成
    $form = <<<EOF
<p>ご指定のメールアドレスにURLを送信致しました。<br>
24時間以内にメールに記載されたURLから手続きをお願い致します。
</p>
<div style="text-align:center;">
<button type="button" class="btn btn-primary" onclick="location.href='/{$SERVICE_ROOT}/MyPage/home.php'" style="width: 90%;">戻る</button>
</div>
EOF;

    // テンプレートファイル読み込み
    include dirname(__FILE__).'/../Template/BaseTemplate.php';
}else{
    // メール送信後でなければログイン画面に推移
    header('Location: /'.$SERVICE_ROOT.'/Auth/login.php');
}
