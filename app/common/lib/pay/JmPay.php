<?php


namespace app\common\lib\pay;


class JmPay extends Pay
{
    private $channelId = "bdt02";
    private $mchId = "1009525";
    private $CallbackUrl = "/api/notify.jmpay/callback";
    private $SecretKey = "cdDecdFDC86d1Ec0119976856Fb40c94087207A7cF83b5214ccd0180071b83a222970143aA52dD8169e56a2715577ddd";
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
}