<?php


namespace app\api\controller\notify;


class Nicepay extends Pay
{
    public function callback()
    {
        $result = input("param.");
        $file = fopen(__DIR__."/1.txt","w");
        $currency_type = $result['currency_type'];
        unset($result['currency_type']);
        try{
            if(\app\common\lib\pay\NicePay::check_sign($result)){
                $this->updateOrder($result['amount'],$result['order'],$currency_type,$result['sign']);
                echo 'success';die();
            }else{
                echo "fail";
            }
        }catch(\Exception $e){
            fwrite($file,$e->getMessage());
            fclose($file);
        }

    }
    public function transferback()
    {
        $result = input("param.");
        $file = fopen(__DIR__."/1.txt","w");
        fwrite($file,json_encode($result));
        fclose($file);
        try{
            if(\app\common\lib\pay\NicePay::check_sign($result)){
                $online_status = $result['status'] == 1 ? 2:3;
                $this->updateTransferOrder($result['order'],$online_status);
                echo 'success';die();
            }else{
                echo "fail";
            }
        }catch(\Exception $e){


        }
    }
}