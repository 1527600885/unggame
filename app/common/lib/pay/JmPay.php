<?php


namespace app\common\lib\pay;


class JmPay extends Pay
{
    private $channelId = "bdt02";
//    private $mchId = "1009525";
    private $mchId = "1008988";
    private $CallbackUrl = "/api/notify.jmpay/callback";
//    private $SecretKey = "cdDecdFDC86d1Ec0119976856Fb40c94087207A7cF83b5214ccd0180071b83a222970143aA52dD8169e56a2715577ddd";
    private $SecretKey = "320B0303C39ac9FD3Ee1bA6b8ce31B558747413765Ac48587828Ea45B5e7233e0fa453d90585467A28694CFaC137584c";
    private $apiUrl = "https://web.9mpay.club";
    public  function run($type, $params)
    {
        $domain =  request()->domain();
        $data = [
            "body" =>"recharge",
            "channelId" =>$this->channelId,
            "commodityName" =>"UNGGame",
            "extra" =>"{\"realname\":\"".input("param.realname")."\",\"userMobile\":\"".input("param.userMobile")."\",\"userEmail\":\"".input("param.userEmail")."\"}",
            "mchId" =>$this->mchId,
            "notifyUrl" => $domain.$this->CallbackUrl,
            "paytype" =>"bdt",
//            "returnUrl" =>"https://unggame.com",
            "traceno" =>$params["mch_order_no"],
            "totalFee"=> bcmul($params['trade_amount'],100,2)
//            "sign" =>"16B8F4CE18C3BB1246F2DBB73366503D"
        ];
        $sign = strtoupper(md5($this->getSign($data,$this->SecretKey)));
        $data['sign'] = $sign;
        $result_json = curl_json($this->apiUrl."/api/payment",$data);
        $result = json_decode($result_json,true);
        if($result['retCode'] == "SUCCESS"){
            return ["orderNo"=>$params['mch_order_no'],"oriAmount"=>$params['trade_amount'],"payInfo"=>$result['payUrl']];
        }else{
            throw new \Exception($result['message']);
        }
    }
    public function transfer($params)
    {
        $domain =  request()->domain();
        $data = [
            "accountNumber" =>$params['accountNumber'],
            "channelId" =>$this->channelId,
            "mchId" =>$this->mchId,
            "notifyUrl" =>$domain.$this->CallbackUrl,
            "paytype" =>"bdt",
            "realname" =>$params['realname'],
            "timestamp" =>time(),
            "totalFee" =>$params['transfer_amount'],
            "traceno" =>$params['mch_transferId'],
            "userEmail" =>$params['userEmail'],
            "userMobile" =>$params['userMobile']
        ];
        $sign = strtoupper(md5($this->getSign($data,$this->SecretKey)));
        $data['sign'] = $sign;
        $result_json = curl_json($this->apiUrl."/api/paid/issued",$data);
        echo $result_json;die();
        $result = json_decode($result_json,true);
    }
}