<?php
// +----------------------------------------------------------------------
// | OneKeyAdmin [ Believe that you can do better ]
// +----------------------------------------------------------------------
// | Copyright (c) 2020-2023 http://onekeyadmin.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: MUKE <513038996@qq.com>
// +----------------------------------------------------------------------
namespace plugins\withdrawal\admin\controller;

use app\admin\model\User;
use app\api\model\WithdrawalSettings;
use app\common\lib\pay\Pay;
use think\facade\View;
use app\admin\BaseController;
use plugins\withdrawal\admin\model\MkWithdrawal as MkWithdrawalModel;
/**
 * MkWithdrawal管理
 */
class MkWithdrawal extends BaseController
{
    /**
     * 显示资源列表
     */
    public function index()
    {
        if ($this->request->isPost()) {
            $input = input("post.");
            $count = MkWithdrawalModel::withSearch(["keyword"], $input)->count();
            $data  = MkWithdrawalModel::withSearch(["keyword"], $input)->append(["online_status_name","add_times","type_name"])->order($input["prop"], $input["order"])->page($input["page"], $input["pageSize"])->select();
            foreach($data as $k=>$v){
				$v->amount_name=$v->amount."($)";
				$v->money_name=$v->money."(".$v->currency.")";
				$v->charge_name=$v->charge."(".$v->currency.")";
				$v->add_times=date("Y-m-d H:i:s",$v->add_time);
				$v->pay_type = 1;
				if($v->payment_name &&  WithdrawalSettings::where("name",$v->payment_name)->value("pay_type") == 2)
				{
				   $v->pay_type = 2;
                }
				if($v->other){
					$other=json_decode($v->other,true);
					$v->other_name=null;
					foreach($other as $key=>$value){
						$v->other_name.=$key.":".$value."</br>";
					}
				}
//				if($v->type>1){
//					if($v->online_status==0){
//						$v->online_status_name="未代付";
//					}elseif($v->online_status==1){
//						$v->online_status_name="<span style='color:#FF8000;'>代付中</span>";
//					}elseif($v->online_status==2){
//						$v->online_status_name="<span style='color:#0080FF;'>代付完成</span>";
//					}elseif($v->online_status==3){
//						$v->online_status_name="<span style='color:#FF0000;'>代付失败</span>";
//					}
//				}else{
//					$v->online_status_name="数字货币自己支付";
//				}
			}
			return json(["status" => "success", "message" => "请求成功", "data" => $data, "count" => $count]);
        } else {
            return View::fetch();
        }
    }
    
    /**
     * 保存新建的资源
     */
    public function save()
    {
        if ($this->request->isPost()) {
            MkWithdrawalModel::create(input("post."));
            return json(["status" => "success", "message" => "添加成功"]);
        }
    }
    
    /**
     * 保存更新的资源
     */
    public function update()
    {
        if ($this->request->isPost()) {
            MkWithdrawalModel::update(input("post."));
            return json(["status" => "success", "message" => "修改成功"]);
        }
    }
    
    /**
     * 删除指定资源
     */
    public function delete()
    {
        if ($this->request->isPost()) {
            MkWithdrawalModel::destroy(input("post.ids"));
            return json(["status" => "success", "message" => "删除成功"]);
        }
    }
    public function check()
    {
        if ($this->request->isPost()) {
            $input = input("post.");
            $input['status_time'] = $input['pay_time'] =  time();
            $data = MkWithdrawalModel::where("id",$input['id'])->find();
            $data->save($input);
            if($data->online_status){
                User::where("id",$data->uid)->inc("balance",$data->amount)->update();
                $userInfo = User::where("id",$data->uid)->find();
                $content='{capital.withdrawalfailed}'.$data->amount.'{capital.money}';
                $admin_content='用户'.$userInfo->nickname.'提现失败,退款$'.$data->amount;
                capital_flow($data->uid,$data->id,11,1,$data->amount,$userInfo->balance,$content,$admin_content);
            }

            return json(["status" => "success", "message" => "审核成功"]);
        }
    }
    public function payonline()
    {
        $input = input("post.");
        $data = MkWithdrawalModel::where("id",$input['id'])->find();
        if(!$data || $data->status!=0)  return json(["status" => "failed", "message" => "状态错误"]);
        $setting = WithdrawalSettings::where("name",$data->payment_name)->find();
        if($setting->pay_type !=2) return json(["status" => "failed", "message" => "不支持在线打款"]);
        $pay = Pay::instance($data->payment_name,$data->currency);
        $userInfo = User::where("id",$data->uid)->find();
        $mch_transferId = 'order'.$userInfo['game_account'].time();
        $other = json_decode($data->other,true);
        $pay->transfer([
            "mch_transferId"=>$mch_transferId,
            "transfer_amount"=>$data->money,
            "bank_code"=>$other['bank'],
            "receive_name"=>$other['receive name'],
            "receive_account"=>$other['card number'],
        ]);
        $data->online_status = 1;
        $data->status_time = time();
        $data->merTransferId = $mch_transferId;
        $data->save();
        return json(["status" => "success", "message" => "提交成功"]);
    }
}