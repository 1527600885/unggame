<?php


namespace app\common\lib\pay;


class RainbowPay extends Pay
{
    protected $mchId = "2z594272";
    protected $passageId = '101';
    private  $notifyUrl = "/api/notify.rainbowpay/callback";
    private $key = "cf7b2d5be4074731b51db5ce7624a208";
    private $apiUrl = "http://apis.rainbowpay.xyz/client";
    public  function run($type, $params)
    {
        $domain =  request()->domain();
        $data = [
            "mchId"=>$this->mchId,
            "passageId"=>$this->passageId,
            "amount"=>$params['trade_amount'],
            "orderNo"=>$params["mch_order_no"],
            "notify_url"=>$domain.$this->notifyUrl."?currency_type=".$this->currency_type,//回调地址
        ];
        $sign = $this->getSign($data,$this->key);
        $data['sign'] = $sign;
        $result_json = curl($this->apiUrl."/collect/create",$data);
        $result = json_decode($result_json,true);
        if($result['code'] == 200)
        {
            return ["orderNo"=>$params['mch_order_no'],"oriAmount"=>$params['trade_amount'],"payInfo"=>$result['data']['payUrl']];
        }else{
            throw new \Exception($result['msg']);
        }
    }
    public function getKey()
    {
        return $this->key;
    }
}