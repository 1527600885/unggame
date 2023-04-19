<?php


namespace app\common\lib\pay;


class OePay extends Pay
{
    protected $apiUrl = 'https://openapi.oepay.co.in';
    public function run($type, $params)
    {
        $data = [
            'orderNo' => $params['mch_order_no'],
            'amount' => $params['trade_amount'],
            'firstname' => input("param.realname"),
            'mobile' => input("param.mobile"),
            'email' => input("param.email"),
            'surl' => "https://www.unggame.com",
            'furl' => 'https://www.unggame.com',
            'remark' => 'test',
        ];
        $result_json = curl($this->apiUrl."/gold-pay/portal/createH5PayLink",$data);
        $result = json_decode($result_json,true);
    }
}