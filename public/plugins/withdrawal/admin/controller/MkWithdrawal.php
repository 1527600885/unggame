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
            MkWithdrawalModel::where("id",$input['id'])->update($input);
            return json(["status" => "success", "message" => "审核成功"]);
        }
    }
}