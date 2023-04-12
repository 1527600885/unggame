<?php


namespace app\api\controller\notify;


class Rainbowpay extends Pay
{
    public function callback()
    {
        $result = input("param.");
        $currency_type = $result['currency_type'];
        unset($result['currency_type']);
        $pay = new \app\common\lib\pay\RainbowPay("");
        $result_sign = $result['sign'];
        unset($result['sign']);
        $sign = $pay->getSign($result,$pay->getKey());
        if($result_sign == $sign)
        {
            $this->updateOrder($result['amount'],$result['orderNo'],$currency_type,$sign);
            echo "success";die();
        }else{
            echo "fail";die();
        }
    }
}