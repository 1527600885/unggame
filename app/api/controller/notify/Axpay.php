<?php


namespace app\api\controller\notify;


class Axpay extends Pay
{
    public function callback()
    {
        $result = input("param.");
        $file = fopen(__DIR__."/3.txt","w");
        fwrite($file,json_encode($result));
        fclose($file);
        try{
            $axPay = new \app\common\lib\pay\AxPay("");
            $sign = $result['Sign'];
            unset($result['Sign']);
            if($sign == $axPay->getSign($result,$axPay->SecretKey,"SecretKey")){
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
    public function transferback()
    {
        $result = input("param.");
        $file = fopen(__DIR__."/3.txt","w");
        fwrite($file,json_encode($result));
        fclose($file);
        try{
            $axPay = new \app\common\lib\pay\AxPay("");
            $sign = $result['Sign'];
            unset($result['Sign']);
            if($sign == $axPay->getSign($result,$axPay->SecretKey,"SecretKey")){
                if($result['Status'] == 4 || $result['Status'] == 16){
                    $online_status = $result['Status'] == 4 ? 2:3;
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