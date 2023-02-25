<?php


namespace app\api\controller\notify;


use app\api\model\Order as Ordermodel;
use app\api\model\User as UserModel;
use think\facade\Db;

class Wowpay
{
    public function callBack()
    {
        $data = input("param.");
        $result = json_decode($data,true);
        if(isset($result['tradeResult'])&&$result['tradeResult'] == 1){
            $sign = $result['sign'];
            unset($result['sign'],$result['signType']);
            $wowPay = new \app\common\lib\pay\WowPay("");
            $key =  $wowPay['debug'] ?  $wowPay['testconfig'][$result['merRetMsg']]['key'] : $wowPay['config'][$result['merRetMsg']]["key"];
            if($sign == $wowPay->getSign($result,$key)){
                $rate=\app\api\model\CurrencyAll::where('name',$data['currencyvalue'])->value('rate');
                $amount = round($result['amount']/$rate,2);
                $orderinfo=Ordermodel::where(['mer_order_no'=>$result['mchOrderNo'],'status'=>0])->find();
                if(abs($amount-$orderinfo->money) < 0.03){
                        $amount = $orderinfo->money;
                }
                if($orderinfo){
                    $userinfo=UserModel::where('id',$orderinfo->uid)->find();
                    Db::transaction(function () use ($result,$orderinfo,$userinfo,$amount) {
                        Ordermodel::where('mer_order_no',$result['mchOrderNo'])->update(['time2'=>strtotime($result['orderDate']),'status'=>1,'sign'=>$result['sign'],"realAmount"=>$amount]);
                        UserModel::where('id',$userinfo->id)->inc('balance',$amount)->update();
                    });
                    $content='{capital.content}'.$amount.'{capital.money}';
                    $admin_content='用户'.$userinfo->nickname.'通过在线充值获得'.$amount.'美元';
                    capital_flow($userinfo->id,$orderinfo->id,1,1,$amount,bcadd($userinfo->balance."",$amount."",2),$content,$admin_content);
                    echo 'success';
                }else{
                    echo 'success';
                }
            }

        }
    }
}