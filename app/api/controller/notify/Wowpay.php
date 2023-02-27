<?php


namespace app\api\controller\notify;


use app\api\model\Order as Ordermodel;
use app\api\model\User as UserModel;
use think\facade\Db;

class Wowpay
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
                $rate=\app\api\model\CurrencyAll::where('name',$result['merRetMsg'])->value('rate');
                $amount = round($result['amount']/$rate,2);
                $orderinfo=Ordermodel::where(['mer_order_no'=>$result['mchOrderNo'],'status'=>0])->find();
                if(abs($amount-$orderinfo->money) < 0.03){
                    $amount = $orderinfo->money;
                }
                if($orderinfo){
                    $userinfo=UserModel::where('id',$orderinfo->uid)->find();
                    Db::transaction(function () use ($result,$orderinfo,$userinfo,$amount,$sign) {
                        Ordermodel::where('mer_order_no',$result['mchOrderNo'])->update(['time2'=>strtotime($result['orderDate']),'status'=>1,'sign'=>$sign,"realAmount"=>$amount]);
                        UserModel::where('id',$userinfo->id)->inc('balance',$amount)->update();
                    });
                    $content='{capital.content}'.$amount.'{capital.money}';
                    $admin_content='用户'.$userinfo->nickname.'通过在线充值获得'.$amount.'美元';
                    capital_flow($userinfo->id,$orderinfo->id,1,1,$amount,bcadd($userinfo->balance."",$amount."",2),$content,$admin_content);
                    echo 'success';die();
                }else{
                    echo 'success';die();
                }
            }

        }else{
            echo "fail";die();
        }
    }
}