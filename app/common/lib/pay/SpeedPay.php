<?php


namespace app\common\lib\pay;


class SpeedPay extends Pay
{
    public $secret = "29efad039f90402f86e31308ab1f9da7";
    protected $merchantId = "PM100999";
    protected $channel="spay";
    protected $callbackUrl = "/api/notify.speedpay/callback";
    protected $transferback = "/api/notify.speedpay/transferback";
    protected $apiUrl = "https://spayin.com";
    public function run($type,$params)
    {
        $domain =  request()->domain();
        $data = [
            "callbackUrl" =>$domain.$this->callbackUrl,
            "merchantId" =>$this->merchantId,
            "merchantOrderId" =>$params['mch_order_no'],
            "orderAmount" =>$params['trade_amount'],
            "param" =>$this->currency_type,
            "timeMillis" =>bcmul(microtime(true),1000)
        ];
        $sign = $this->getSign($data,$this->secret,"secret");
        $data['sign'] = strtoupper($sign);
        $result_json = curl_json($this->apiUrl."/api/payment/recharge",$data);
        $result = json_decode($result_json,true);
        if($result['code'] != 1){
            return ["orderNo"=>$params['mch_order_no'],"oriAmount"=>$params['trade_amount'],"payInfo"=>$result['data']];
        }else{
            echo $result_json;die();
        }
    }
    public function transfer($param)
    {
        $domain =  request()->domain();
        $data = [
            "amount" =>$param['transfer_amount'],
            "callbackUrl" =>$domain.$this->transferback,
            "cardNo" =>$param['cardNo'],
            "ifsc" =>$param['ifsc'],
            "merchantId" =>$this->merchantId,
            "merchantOrderId" =>$param['mch_transferId'],
            "param" => "UNG GAME WITHDRAWL",
            "personName" =>$param['personName'],
            "timeMillis" =>bcmul(microtime(true),1000)
        ];
        $sign = $this->getSign($data,$this->secret,"secret");
        $data['sign'] = strtoupper($sign);
        $result_json = curl_json($this->apiUrl."/api/payment/withdrawal",$data);
        $result = json_decode($result_json,true);
        if($result['code'] == 1){
            return $result;
        }else{
            echo $result_json;die();
        }
    }
}