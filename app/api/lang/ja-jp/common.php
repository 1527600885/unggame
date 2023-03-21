<?php
return array(
	'system'=> [
		'iperror' => 'あなたがアクセス可能な領域にいないので、あなたのアクセスはシステムによって妨げられました',
		'id' => 'パラメータエラー',
		'success' => '荷積み',
		'code' => '検証コード',
		'hi' => 'こんにちは',
		'sendcode' => '確認コードを送ってください',
		'page' => 'ページ',
		'email_configure_error'=> 'システムがメールボックスを構成していない',
		'authenticate'=>'メールボックスSMTPエラー：ログインに失敗しました。アカウントとパスワードが正しく入力されていることを確認してください。',
		'connect_host'=>'メールボックスSMTPエラー：SMTPホストに接続できません。SMTP、ポート、送信方法が正しいかどうか確認してください。',
		'data_not_accepted'=>'メールボックスSMTPエラー：データは受け入れませんでした。',
		'empty_message'=>'メールボックスの内容は空にできません',
		'encoding'=>'不明なコード',
		'execute'=>'メールボックスが失敗しました',
		'file_access'=>'メールボックスがファイルにアクセスできません',
		'file_open'=>'メールボックスがファイルにアクセスできません',
		'from_failed'=>'メールアドレスエラー',
		'instantiate'=>'不明な関数呼び出し',
		'invalid_email'=>'送信されない電子メール、無効なメールアドレス',
		'mailer_not_supported'=>'メールボックス送信クライアント',
		'provide_address'=>'メールボックスは、少なくとも1つの受信者アドレスを提供しなければなりません',
		'recipients_failed'=>'電子メールSMTPエラー',
		'signing'=>'メール署名エラー',
		'smtp_connect_failed'=>'メールボックスSMTP接続',
		'smtp_error'=>'メールボックスSMTPサーバエラー',
		'variable_set'=>'Mailbox変数を設定またはリセットできません',
		'nickname_default'=>'無名',
		'describe'=>'この男はとても怠け者で、何も残していない。',
		'operation_succeeded'=>'操作が成功',
		'operation_failed'=>'操作失敗',
		'currency_name'=>'デジタル通貨による支払い',
		'online_name'=>'オンライン決済',
		'creditcards'=>'クレジットカード払い',
		'setting_succeeded'=>'設定に成功しました'
	],
	'user'=> [
		'accountnot' => 'アカウントが存在しません',
		'wrong'=> 'パスワードまたはアカウントが間違っています',
		'shield'=> 'あなたのアカウントはブロックされます',
		'login'=> 'ログイン成功',
		'email'=> 'メール番号の書式が正しくない',
		'mobileEmpty'=> '携帯電話番号を入力してください',
		'accountEmpty'=> '口座番号を記入してください',
		'captchaEmpty'=> '確認コードに記入してください',
		'captchaError'=> '不正な検証コード',
		'nicknameEmpty'=> 'ニックネームは空であるはずがない',
		'nicknameError'=> 'ニックネームは40を超えることはできない',
		'describeError'=> '署名数は255を超えない',
		'passwordEmpty'=> 'パスワードを入力してください',
		'passwordconfirm'=> 'パスワードが異なる',
		'passwordMin'=> 'パスワードは6未満です',
		'passwordMax'=> 'パスワードは12を超えない',
		'passwordAlphaNum'=>'数字と英字のみ入力可能',
		'tokenEmpty'=> 'ログインしていません！',
		'tokenExpired'=> '期限切れ！',
		'tokenError'=> 'トークン検証エラー!',
		'accountBlocked'=> 'このアカウントはブロックされます！',
		'notRegister'=> 'ユーザーはまだ登録されていません',
		'emailoccupy'=>'メール番号が登録されました',
		'userregister'=>'ユーザー登録',
		'codeerror'=>'電子メールの検証コードエラー',
		'emailerror'=>'メール番号が登録されました',
		'registersuccess'=>'ログイン成功',
		'unregistered'=>'メール登録',
		'forgot'=>'パスワード忘れ',
		'mobileexistence'=>'この携帯番号は登録済みです',
		'mobilesuccess'=>'バインド成功',
		'sex'=>'性別を選択してください',
		'pay_paasword_require'=>'支払パスワードは必須です',
		'pay_paasword_length'=>'支払パスワードは6桁でなければなりません',
		'bindemail'=>'バインドメールボックス',
		'inviteusers'=>'招待ユーザー',
		'inviteregister'=>'登録',
		'safetylow'=>'低い',
		'safetycommonly'=>'ありふれた',
		'safetyhigh'=>'高い',
		'safetyperfect'=>'完璧な',
		'userverify'=>'Please verify your account first',
	],
	'game'=>[
		'money_funds'=>'資金不足',
		'run_game'=>'正常にスタートしてください',
		'synchronizing_funds'=>'資金同期完了',
		'no_funds_synchronized'=>'資金を同期できません',
	],
	'order'=>[
		'mobileEmpty'=>'バインド電話番号',
		'toolittle'=>'デジタル通貨の金額が小さすぎる！',
		'toofast'=>'支払いが早すぎて、ちょっと休んで、帰ってきます',
		'ordersuccess'=>'支払いに成功しました。管理者の承認を待ってください。。。',
		'ordererror'=>'支払いに失敗しました。再試行してください',
		'placedata'=>'メダルを買う',
		'placesuccess'=>'お待ちください。。。',
		'placeerror'=>'管理者が支払問題を処理するのを待っていてください！',
		'currencyvalue'=>'チャージする通貨を選択してください',
		'currencyerror'=>'申し訳ありませんが、この通貨の取引は現在サポートされていません',
	],
	'capital'=>[
		'user'=>'ユーザー',
		'content'=>'オンラインチャージによる取得',
		'money'=>'ドル',
		'gamecontento'=>'ゲームをする',
		'gamecontentt'=>'資金の増加',
		'gamecontenth'=>'資金が減る',
		'gamecontentf'=>'資本不変',
	],
	'recharge'=>[
		'digital'=>'USDT/BTC/ETH じゅうでん',
		'cash'=>'現金チャージ',
		'Credit'=>'クレジットカードチャージ'
	]
);
?>