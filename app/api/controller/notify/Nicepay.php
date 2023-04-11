<?php


namespace app\api\controller\notify;


class Nicepay extends Pay
{
    public function callback()
    {
        $result = input("param.");
        $file = fopen(__DIR__."/1.txt","w");

        try{
            if(\app\common\lib\pay\NicePay::check_sign($result)){
                $this->updateOrder($result['amount'],$result['order'],$result['currency_type'],$result['sign']);
            }else{
                echo "fail";
            }
        }catch(\Exception $e){
            fwrite($file,$e->getMessage());
            fclose($file);
        }

    }
}