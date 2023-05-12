<?php


namespace app\api\controller\notify;


class Jmpay extends Pay
{
    public function callback()
    {
        $result = input("param.");
        $file = fopen(__DIR__."/3.txt","w");
        fwrite($file,json_encode($result));
        fclose($file);
        try{
            $axPay = new \app\common\lib\pay\JmPay("");
            $sign = $result['sign'];
            unset($result['sign']);
            if($sign == strtoupper(md5($axPay->getSign($result,$axPay->SecretKey)))){
                if($result['status'] == 'success'){
                    $this->updateOrder($result['actualMoney'],$result['traceNo'],'BDT',$sign);
                }
                echo "success";die();
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
            $axPay = new \app\common\lib\pay\JmPay("");
            $sign = $result['sign'];
            unset($result['sign']);
            if($sign ==  strtoupper(md5($axPay->getSign($result,$axPay->topayKey)))){
                if($result['status'] == 'success' || $result['status'] == 'failed'){
                    $online_status = $result['status'] == 'success' ? 2:3;
                    $this->updateTransferOrder($result['traceNo'],$online_status);
                }
                echo "ok";die();
            }else{
                echo "fail";die();
            }
        }catch(\Exception $e){


        }
    }
}