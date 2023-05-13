<?php


namespace app\api\controller\notify;


class Htpay extends Pay
{
    public function callback()
    {
        $result = input("param.");
        $file = fopen(__DIR__."/3.txt","w");
        fwrite($file,json_encode($result));
        fclose($file);
        try{
            $axPay = new \app\common\lib\pay\HtPay("");
            $sign = $result['sign'];
            unset($result['sign']);
            foreach ($result as $k=>$v){
                if(empty($v)){
                    unset($result[$k]);
                }
            }
            if($sign == $axPay->getSign($result,$axPay->md5Key)){
                if($result['status'] == 'SUCCESS'){
                    $this->updateOrder($result['order_amount'],$result['mer_order_no'],'PHP',$sign);
                }
                echo "SUCCESS";die();
            }else{
                echo "FAIL";die();
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
            $axPay = new \app\common\lib\pay\HtPay("");
            $sign = $result['sign'];
            unset($result['sign']);
            foreach ($result as $k=>$v){
                if(empty($v)){
                    unset($result[$k]);
                }
            }
            if($sign == $axPay->getSign($result,$axPay->md5Key)){
                if($result['status'] == 'SUCCESS' || $result['status'] == 'FAIL'){
                    $online_status = $result['status'] == 'SUCCESS' ? 2:3;
                    $this->updateTransferOrder($result['mer_order_no'],$online_status);
                }

                echo "SUCCESS";die();
            }else{
                echo "FAIL";die();
            }
        }catch(\Exception $e){


        }
    }
}