<?php


namespace app\api\controller\notify;


use app\api\model\Order as Ordermodel;
use app\api\model\User;
use app\api\model\User as UserModel;
use app\api\model\Withdrawal;
use think\facade\Db;

class Wowpay extends Pay
{
    public function callBack()
    {
        $result = input("param.");
        // $result = json_decode($data,true);
        if(isset($result['tradeResult'])&&$result['tradeResult'] == 1){
            $sign = $result['sign'];
            unset($result['sign'],$result['signType']);
            $wowPay = new \app\common\lib\pay\WowPay("");
            $key =  $wowPay->payConfig['debug'] ?  $wowPay->payConfig['testconfig'][$result['merRetMsg']]['key'] : $wowPay->payConfig['config'][$result['merRetMsg']]["key"];
            if($sign == $wowPay->getSign($result,$key)){
                $this->updateOrder($result['amount'],$result['mchOrderNo'],$result['merRetMsg'],$sign);
                echo 'success';die();
            }

        }else{
            echo "fail";die();
        }
    }
    public function transferback()
    {
        $result = input("param.");
        // $result = json_decode($data,true);
        if( isset($result['tradeResult'])){
            $sign = $result['sign'];
            unset($result['sign'],$result['signType']);
            $withdrawl = Withdrawal::where("merTransferId",$result['merTransferId'])->find();
            $currency = $withdrawl['currency'];
            $wowPay = new \app\common\lib\pay\WowPay("");
            $key =  $wowPay->payConfig['debug'] ?  $wowPay->payConfig['testconfig'][$currency]['key'] : $wowPay->payConfig['config'][$currency]["key"];
            if($sign == $wowPay->getSign($result,$key)){
                if($result['tradeResult'] == 1 || $result['tradeResult'] == 2){
                    $online_status = $result['tradeResult'] == 1 ? 2 : 3;
                    $this->updateTransferOrder($result['merTransferId'],$online_status);
                }
            }
            echo "success";
        }
        echo "success";
    }
}