<?php


namespace app\api\controller\notify;


class Surepay extends Pay
{
    public function callback()
    {
        $result = input("param.");
        $file = fopen(__DIR__."/3.txt","w");
        fwrite($file,json_encode($result));
        fclose($file);
        $pay = new \app\common\lib\pay\SurePay("");
        $sign = md5($result['merchant'].$result['amount'].$result['status'].$pay->callbackkey.$result['trxno']);
        if($sign == $result['token']){
            if($result['status'] == 1) {
                $this->updateOrder($result['amount'],$result['refid'],"MYR",$sign);
                echo 'SUCCESS';
            }
            else {
                echo 'FAIL';
            }
        }else{
            echo 'FAIL';
        }

    }
}