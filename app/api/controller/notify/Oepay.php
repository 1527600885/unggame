<?php


namespace app\api\controller\notify;


class Oepay extends Pay
{
    public function callback()
    {
        $result = input("param.");
        $file = fopen(__DIR__."/3.txt","w");
        fwrite($file,json_encode($result));
        fclose($file);
        if (\app\common\lib\pay\OePay::rsaVerify($result, $_SERVER['HTTP_X_SIGN'])) {
            if($result['status'] == 1)
            {
                $this->updateOrder($result['amount'],$result['orderNo'],'PHP',$_SERVER['HTTP_X_SIGN']);
            }
        }

    }
    public function transferback()
    {
        $result = input("param.");
        $file = fopen(__DIR__."/3.txt","w");
        fwrite($file,json_encode($result));
        fclose($file);
        try{
            if(\app\common\lib\pay\OePay::rsaVerify($result, $_SERVER['HTTP_X_SIGN'])){
                if($result['status'] == 0 || $result['status'] == 1){
                    $online_status = $result['status'] == 1? 2:3;
                    $this->updateTransferOrder($result['OrderNo'],$online_status);
                }
                echo "ok";die();
            }else{
                echo "fail";die();
            }
        }catch(\Exception $e){


        }
    }
}