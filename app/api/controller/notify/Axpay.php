<?php


namespace app\api\controller\notify;


class Axpay extends Pay
{
    public function callback()
    {
        $result = input("param.");
        $file = fopen(__DIR__."/1.txt","w");
        fwrite($file,json_encode($result));
        fclose($file);
        try{
            $axPay = new \app\common\lib\pay\AxPay("");
            $sign = $result['sign'];
            unset($result['sign']);
            if($sign = $axPay->getSign($result,$axPay->SecretKey,"SecretKey")){
                if($result['Status'] == 4){
                    $this->updateOrder($result['Amount'],$result['OrderNo'],$result['Ext'],$sign);
                }
                echo "ok";die();
            }else{
                echo "fail";die();
            }
        }catch(\Exception $e){


        }

    }
}