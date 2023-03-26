<?php
return array(
	'system'=> [
		'iperror' => '액세스 가능 영역이 없으므로 액세스가 차단되었습니다.',
		'id' => '매개변수 오류',
		'success' => '로드 성공',
		'code' => '인증 코드',
		'hi' => '안녕하세요.',
		'sendcode' => '인증번호 보내기',
		'page' => '페이지',
		'email_configure_error'=> '시스템이 메일박스를 설정하지 않았습니다.',
		'authenticate'=>'메일박스 SMTP 오류: 로그인에 실패했습니다.계정과 비밀번호 입력이 정확한지 확인하십시오.',
		'connect_host'=>'메일박스 SMTP 오류: SMTP 호스트에 연결할 수 없습니다.SMTP, 포트 및 전송 방법이 올바른지 확인하십시오.',
		'data_not_accepted'=>'메일박스 SMTP 오류: 데이터를 받지 않았습니다.',
		'empty_message'=>'메일박스 내용이 비어 있으면 안 됩니다.',
		'encoding'=>'메일박스 알 수 없는 코드',
		'execute'=>'메일박스가 실행되지 않음',
		'file_access'=>'메일박스에서 파일에 접근할 수 없음',
		'file_open'=>'메일박스에서 파일에 접근할 수 없음',
		'from_failed'=>'이메일 주소 오류',
		'instantiate'=>'메일박스 알 수 없는 함수 호출',
		'invalid_email'=>'전자 메일이 전송되지 않았습니다. 전자 메일 주소가 잘못되었습니다.',
		'mailer_not_supported'=>'메일박스 전송 클라이언트가 지원되지 않음',
		'provide_address'=>'메일박스는 최소한 수신자 주소를 제공해야 한다',
		'recipients_failed'=>'e-메일 SMTP 오류, 받는 사람 주소 오류',
		'signing'=>'전자 메일 서명 오류',
		'smtp_connect_failed'=>'메일박스 SMTP 연결 () 실패',
		'smtp_error'=>'메일박스 SMTP 서버 오류',
		'variable_set'=>'메일박스에서 변수를 설정하거나 재설정할 수 없습니다',
		'nickname_default'=>'이름 없음',
		'describe'=>'이 사람은 게으르고 아무것도 남지 않았다.',
		'operation_succeeded'=>'작업 성공',
		'operation_failed'=>'작업 실패',
		'currency_name'=>'디지털 화폐 지불',
		'online_name'=>'온라인 결제',
		'creditcards'=>'신용카드 결제',
		'setting_succeeded'=>'설정 성공'
	],
	'user'=> [
		'accountnot' => '계정이 없습니다.',
		'wrong'=> '암호 또는 계정 오류',
		'shield'=> '계정이 차단되었습니다.',
		'login'=> '로그인 성공',
		'email'=> '이메일 번호 형식이 잘못되었습니다.',
		'mobileEmpty'=> '당신의 핸드폰 번호를 기입해 주십시오',
		'accountEmpty'=> '계좌번호를 기입해 주십시오',
		'captchaEmpty'=> '인증 코드를 입력하십시오.',
		'captchaError'=> '인증번호가 잘못되었습니다.',
		'nicknameEmpty'=> '닉네임은 비워둘 수 없습니다.',
		'nicknameError'=> '닉네임은 40을 넘으면 안 돼요.',
		'describeError'=> '서명 수는 255개를 초과할 수 없습니다.',
		'passwordEmpty'=> '비밀번호를 입력하세요',
		'passwordconfirm'=> '암호가 다름',
		'passwordMin'=> '암호는 6 이하여야 합니다.',
		'passwordMax'=> '비밀번호는 12을 초과할 수 없습니다.',
		'passwordAlphaNum'=>'숫자와 영문자만 입력할 수 있습니다.',
		'tokenEmpty'=> '로그인하지 않았습니다!',
		'tokenExpired'=> '토켄 만료!',
		'tokenError'=> '토큰 검증 오류!',
		'accountBlocked'=> '이 계정이 차단되었습니다!',
		'notRegister'=> '사용자가 아직 등록되지 않았습니다.',
		'emailoccupy'=>'전자 메일 번호가 등록되었습니다.',
		'userregister'=>'사용자 등록',
		'codeerror'=>'e-메일 인증 코드 오류',
		'emailerror'=>'전자 메일 번호가 등록되었습니다.',
		'registersuccess'=>'로그인 성공',
		'unregistered'=>'전자 메일이 등록되지 않았습니다.',
		'forgot'=>'비밀번호를 잊어버리다',
		'mobileexistence'=>'이 핸드폰 번호는 이미 등록되었습니다.',
		'mobilesuccess'=>'바인딩 성공',
		'sex'=>'성별을 선택하세요',
		'pay_paasword_require'=>'결제 비밀번호가 필요합니다.',
		'pay_paasword_length'=>'결제 비밀번호는 6자리 숫자여야 합니다.',
		'bindemail'=>'메일박스 바인딩',
		'inviteusers'=>'사용자 초대',
		'inviteregister'=>'등록',
		'safetylow'=>'낮음',
		'safetycommonly'=>'통상적으로',
		'safetyhigh'=>'높음',
		'safetyperfect'=>'완벽했어',
		'addresseror' =>'Blockchain address error',
		'toyouself'   =>'Cannot transfer or gift to yoursel',
		'UNGinsufficient' => 'Your UNG coin balance is insufficient',
		'pay_paasword_error' => 'Transaction password error',
		'buy_limit_error' => 'Less than the minimum purchase quantity',
		'ung_Insufficient' =>'Insufficient UNG coins',
		'banlance_none' => 'Your balance is insufficient',
		'buy_field' => 'Purchase failed',
		'ungaddressempty' =>'Blockchain address cannot be empty',
        'ungaddresserror' =>'Blockchain address error',
        'quantityempty'   =>'Quantity cannot be empty',
        'quantitynumber'  =>'Quantity must be numeric',
        'quantityerror'   =>'quantity error',
        'userverify'=>'Please verify your account first',
        "realnameverification" =>"Real-name authentication required for UNG transfer and redemption."
	],
	'game'=>[
		'money_funds'=>'자금 부족',
		'run_game'=>'시작 성공, 기다려 주십시오',
		'synchronizing_funds'=>'자금 동시 완성',
		'no_funds_synchronized'=>'자금을 동기화할 수 없음',
	],
	'order'=>[
		'mobileEmpty'=>'바인딩 전화 번호',
		'toolittle'=>'디지털 화폐 금액이 너무 작다!',
		'toofast'=>'결제가 너무 빨라서 좀 쉬었다가 돌아오세요',
		'ordersuccess'=>'결제가 성공했습니다. 관리자의 승인을 기다리십시오...',
		'ordererror'=>'결제 실패, 다시 시도하십시오.',
		'placedata'=>'게임머니 구매',
		'placesuccess'=>'잠시만요...',
		'placeerror'=>'관리자가 결제 문제를 처리할 때까지 기다리십시오.',
		'currencyvalue'=>'충전할 화폐를 선택하세요',
		'currencyerror'=>'죄송합니다. 현재 이 통화의 거래가 지원되지 않습니다.',
	],
	'capital'=>[
		'user'=>'사용자',
		'content'=>'온라인 충전을 통해 획득',
		'money'=>'달러',
		'gamecontento'=>'게임을 하다',
		'gamecontentt'=>'자금의 증가',
		'gamecontenth'=>'자금 감소',
		'gamecontentf'=>'자본은 변하지 않는다',
	],
	'recharge'=>[
		'digital'=>'USDT/BTC/ETH 충전',
		'cash'=>'현금 충전',
		'Credit'=>'신용카드 충전'
	]
);
?>