<?php


namespace app\api\controller\notify;


class Rainbowpay extends Pay
{
    public function callback()
    {
        $result = input("param.");
        $file = fopen(__DIR__."/1.txt","w");
        fwrite($file,json_encode($result));
        fclose($file);
        $currency_type = $result['otherData'];
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