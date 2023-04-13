<?php


namespace app\common\lib\pay;


class AxPay extends Pay
{
    protected $AccessKey = "MX8ZY9JBeoFn9GVapP46C2lp5a5";
    protected $PayChannelId = "103";
    protected $CallbackUrl = "";
    protected $SecretKey = "JkKEA3eNZ2TlAeBbKpeJHR52znkJ5XhqkJBw2mKP";
    public  function run($type, $params)
    {
        $domain =  request()->domain();
        $data = [
            "Timestamp"=>time(),
            "AccessKey"=>$this->AccessKey,
            "PayChannelId"=>$this->PayChannelId,
            "OrderNo"=>$params["mch_order_no"],
            "Amount"=>$params['trade_amount'],
            "otherData"=>$this->currency_type,
            "CallbackUrl"=>$domain.$this->CallbackUrl,//回调地址,
        ];
        $sign = $this->getSign($data,$this->SecretKey,"SecretKey");
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
}