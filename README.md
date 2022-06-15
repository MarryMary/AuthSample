# 認証システムサンプル
## 概要
今回は一番オーソドックスなメールアドレス + パスワードの認証に加え

* GoogleでのSSO（シングル・サインオン）
* Google Authenticatiorによる2段階認証（多段階認証）
  
を実装しています。  
## GoogleでのSSO 
今回はメールアドレス・パスワードでの認証を基準としているため、Googleアカウントを使ったログインの場合は、Googleのメールアドレスとユーザー名を使用した上で欠損した情報（パスワードやアカウント画像等）を追加で入力する方式にしています。  
また、SSOには私のアプリ開発用アカウントを使用していますが、本格的に導入する際はどのアカウントを使用するのか慎重に検討する必要があります。  
## Google Authenticatiorによる2ファクター認証  
多くの二段階認証用トークン発行アプリがありますが、今回はGoogle Authenticatiorを選択しました。  
また、Google Authenticatiorはアプリを削除するとログインができなくなってしまう(=トークンが発行できなくなってしまう)仕様のため、万一アクセス不能になってしまった場合にメールアドレスによる強制突破方法を用意しています。  
  
## データベーステーブル構造
* テーブル名：Auth
### Userテーブル（本登録時）
|テーブル名|条件|               コメント               |
|:---:|:---:|:--------------------------------:|
|id|INT NOT NULL AUTO_INCREMENT|     システム内部で識別するためのIDとして使用します     |
|email|VARCHAR(256) NOT NULL|          メールアドレス最長は254           |
|pass|VARCHAR(255) NOT NULL||
|user_pict|VARCHAR(255) NOT NULL|        ユーザー画像を保存するパスを入力する        |
|GAuthID|VARCHAR(255)| Google SSOで使用される個人識別用のIDをインサートする |
|IsTwoFactor|INT(1) NOT NULL DEFAULT 0|        2段階認証が必要な場合は1、通常は0         |
|TwoFactorSecret|VARCHAR(255)|        2段階認証のシークレットを保存         |
|delete_at|DATETIME|    削除を希望した時点の時間を入力。30日後に物理削除     |
|delete_flag|INT(1) NOT NULL DEFAULT 0|        論理削除時にフラグを1に、通常は0         |
---

### PreUserテーブル（仮登録時）
|     テーブル名     |条件|                    コメント                    |
|:-------------:|:---:|:------------------------------------------:|
|      id       |INT NOT NULL AUTO_INCREMENT|          システム内部で識別するためのIDとして使用します          |
|  user_token   |VARCHAR(255) NOT NULL|   アクティベート用のUUIDをインサートします。UUIDはPHPで生成します。   |
|email|VARCHAR(256) NOT NULL|               メールアドレス最長は254                |
|  register_at  |DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP|          仮登録時の日時を入力します。24時間で物理削除           |
| register_type |INT(1) NOT NULL| 仮登録を0、パスワード忘れを1、メール更新を2、強制2段階認証突破を3で登録します。 |
---

## JavaScriptについて
今回は制作時間の都合上formタグによるリダイレクトを使用していますが、少し書き換えるとAjax通信によるログインにも対応可能です。

## 備考
今回は動作が把握しやすいように素のPHPを使用しています。
