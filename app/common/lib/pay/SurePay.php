<?php


namespace app\common\lib\pay;


class SurePay extends Pay
{
    protected $merchant = "Surepay88";
    protected $post_url = "/api/notify.surepay/callback";
    protected $apikey = "4449a5c22d99b4635748df69409eaaebd4099c02";
    protected $apiUrl = "https://sandbox.paymentgt.com";
    protected $bankcode = "10002493";
    public  function run($type, $params)
    {
        $domain =  request()->domain();
        $customer = "cust".time().rand(100,999);

        $data = [
            "merchant"=>$this->merchant,
            "amount"=>$params['trade_amount'],
            "refid"=>$params['mch_order_no'],
            "customer"=>$customer,
            "currency"=>$this->currency_type,
            "bankcode"=>$this->bankcode,
            "clientip"=>"52.55.100.240",
            "post_url"=>$domain.$this->post_url,//回调地址,
            "failed_return_url"=>$domain,
            "return_url"=>$domain
        ];
        $token = md5($data['merchant'].$data['amount'].$data['refid'].$data['customer'].$this->apikey.$this->currency_type."52.55.100.240");
        $data['token'] = $token;
        $result_json = curlNoIpSet($this->apiUrl."/fundtransfer",$data);
        echo $result_json;die();
//        echo $result_json;die();
//        $result_json = curlNoIpSet($this->apiUrl."/payout",$data);
//        $result = json_decode($result_json,true);
//        if($result['status'] == 1)
//        {
//
//            $result = json_decode($result_json,true);
//            return ["orderNo"=>$params['mch_order_no'],"oriAmount"=>$params['trade_amount'],"payInfo"=>$result['data']['payUrl']];
//        }else{
//            throw new \Exception($result['msg']);
//        }
    }
    public function getToken()
    {

    }
}