<?php


namespace app\api\controller\notify;


class Wowpay
{
    public function callBack()
    {
        $data = input("param.");
        $data = '{"tradeResult":"1","oriAmount":"30.00","amount":"30.00","mchId":"111887001","orderNo":"566627045108","mchOrderNo":"orderywzldvzl1677310622","sign":"3e5cb28ae3c3a72ac99fb270f3022681","signType":"MD5","orderDate":"2023-02-25 15:37:03"}';
        $result = json_decode($data,true);
        if(isset($result['tradeResult'])&&$result['tradeResult'] == 1){
            $sign = $result['sign'];
            unset($result['sign'],$result['signType']);
            $wowPay = new \app\common\lib\pay\WowPay();
            if($sign == $wowPay->getSign($result)){
                $amount = $result['amount'];
                echo "验签成功";
            }

        }
    }
}