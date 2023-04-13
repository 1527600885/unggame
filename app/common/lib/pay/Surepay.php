<?php


namespace app\common\lib\pay;


class Surepay extends Pay
{
    protected $merchant = "Surepay88";
    protected $token = "";
    protected $post_url = "/api/notify.surepay/callback";
    protected $apikey = "4449a5c22d99b4635748df69409eaaebd4099c02";
    protected $apiUrl = "https://sandbox.paymentgt.com";
    public  function run($type, $params)
    {
        $domain =  request()->domain();
        $data = [
            "merchant"=>$this->merchant,
            "amount"=>$params['trade_amount'],
            "refid"=>$params["mch_order_no"],
            "customer"=>"cust".time().rand(100,999),
            "currency"=>$this->currency_type,
            "post_url"=>$domain.$this->post_url,//回调地址,
            "bankcode"=>input("param.bankcode"),
            "clientip"=>"52.55.100.240",
            "destbankaccname"=>input("param.destbankaccname"),
            "destbankcode"=>input("param.destbankcode"),
            "destbankaccno"=>input("param.destbankaccno")
        ];
        $token = md5($data['merchant'].$data['amount'].$data['refid'].$data['customer'].$this->apikey.$this->currency_type."52.55.100.240");
        $data['token'] = $token;
        $result_json = curl($this->apiUrl."/payout",$data);
        $result = json_decode($result_json,true);
        if($result['code'] == 200)
        {
            return ["orderNo"=>$params['mch_order_no'],"oriAmount"=>$params['trade_amount'],"payInfo"=>$result['data']['payUrl']];
        }else{
            throw new \Exception($result['msg']);
        }
    }
    public function getToken()
    {

    }
}