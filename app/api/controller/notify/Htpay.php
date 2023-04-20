<?php


namespace app\api\controller\notify;


class Htpay extends Pay
{
    public function callback()
    {
        $result = input("param.");
        $file = fopen(__DIR__."/1.txt","w");
        fwrite($file,json_encode($result));
        fclose($file);
        try{
            $axPay = new \app\common\lib\pay\HtPay("");
            $sign = $result['sign'];
            unset($result['sign']);
            if($sign == $axPay->getSign($result,$axPay->md5Key)){
                echo 'SUCCESS'; die();
                if($result['status'] == 1){
                    $this->updateOrder($result['orderAmount'],$result['merchantOrderId'],$result['param'],$sign);
                }
                echo "success";die();
            }else{
                echo "fail";die();
            }
        }catch(\Exception $e){


        }
    }
}