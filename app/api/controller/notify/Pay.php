<?php


namespace app\api\controller\notify;


use app\api\model\Order as Ordermodel;
use app\api\model\User;
use app\api\model\User as UserModel;
use app\api\model\Withdrawal;
use think\facade\Db;

class Pay
{
    public function updateOrder($amount,$orderno,$type,$sign)
    {
        $rate = \app\api\model\CurrencyAll::where('name',$type)->value('rate');
        $amount = round($amount / $rate,2);
        $orderinfo = Ordermodel::where(['mer_order_no'=>$orderno,'status'=>0])->find();
        if($orderinfo){
            if(abs($amount-$orderinfo->money) < 0.03){
                $amount = $orderinfo->money;
            }
            if($orderinfo){
                $userinfo = UserModel::where('id',$orderinfo->uid)->find();
                Db::transaction(function () use ($orderno,$orderinfo,$userinfo,$amount,$sign) {
                    Ordermodel::where('mer_order_no',$orderno)->update(['time2'=>time(),'status'=>1,'sign'=>$sign,"realAmount"=>$amount]);
                    UserModel::where('id',$userinfo->id)->inc('balance',$amount)->update();
                });
                $content='{capital.content}'.$amount.'{capital.money}';
                $admin_content='用户'.$userinfo->nickname.'通过在线充值获得'.$amount.'美元';
                capital_flow($userinfo->id,$orderinfo->id,1,1,$amount,bcadd($userinfo->balance."",$amount."",2),$content,$admin_content);

            }
        }

    }
    public function updateTransferOrder($orderno,$online_status)
    {
        $withdrawl = Withdrawal::where("merTransferId",$orderno)->find();
        $withdrawl->online_status = $online_status;
        $withdrawl->pay_time = time();
        $withdrawl->save();
        if( $withdrawl->online_status == 3){
            $userInfo =  User::where("id",$withdrawl->uid)->find();
            $userInfo->balance = bcadd($userInfo->balance."",$withdrawl->amount."",4);
            $content='{capital.withdrawalfailed}'.$withdrawl->amount.'{capital.money}';
            $admin_content='用户'.$userInfo->nickname.'提现失败,退款$'.$withdrawl->amount;
            capital_flow($withdrawl->uid,$withdrawl->id,11,1,$withdrawl->amount,$userInfo->balance,$content,$admin_content);
        }
    }
}