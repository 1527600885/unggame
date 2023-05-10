<?php


namespace app\api\controller\notify;


class Rainbowpay extends Pay
{
    public function callback()
    {
        $result = input("param.");
        $file = fopen(__DIR__."/3.txt","w");
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
    public function transferback()
    {
        $result = input("param.");
        $file = fopen(__DIR__."/3.txt","w");
        fwrite($file,json_encode($result));
        fclose($file);
        try{
            $pay = new \app\common\lib\pay\RainbowPay("");
            $result_sign = $result['sign'];
            unset($result['sign']);
            $sign = $pay->getSign($result,$pay->getKey());
            if($result_sign == $sign)
            {
                $online_status = $result['payStatus'] == 1 ? 2:3;
                $this->updateTransferOrder($result['orderNo'],$online_status);
                echo "success";die();
            }else{
                echo "fail";die();
            }
        }catch(\Exception $e){


        }
    }
}