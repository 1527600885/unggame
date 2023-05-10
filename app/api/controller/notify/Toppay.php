<?php


namespace app\api\controller\notify;


class Toppay extends Pay
{
    public function callback()
    {
        $result = json_decode(file_get_contents('php://input'), true);
        $file = fopen(__DIR__."/3.txt","w");
        fwrite($file,json_encode($result));
        fclose($file);
        $model = new \app\common\lib\pay\TopPay("");
        if($model->checkSign($result)){
            if($result['code'] == '00') {
                $this->updateOrder($result['payMoney'],$result['orderNum'],"IDR",$result['platSign']);
                echo 'success';
            }
            else {
                echo 'fail';
            }
        }else{
            echo 'fail';
        }

    }
    public function transferback()
    {
        $result = input("param.");
        $file = fopen(__DIR__."/3.txt","w");
        fwrite($file,json_encode($result));
        fclose($file);
        try{
            $model = new \app\common\lib\pay\TopPay("");
            if($model->checkSign($result)){
                if($result['status'] == 2 || $result['status'] == 4){
                    $online_status = $result['status'] == 2 ? 2:3;
                    $this->updateTransferOrder($result['orderNum'],$online_status);
                }
                echo "success";die();
            }else{
                echo "fail";die();
            }
        }catch(\Exception $e){


        }
    }
}