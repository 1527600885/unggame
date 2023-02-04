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
namespace plugins\order\admin\controller;

use think\facade\View;
use app\admin\BaseController;
use plugins\order\admin\model\MkOrder as MkOrderModel;
use plugins\payment\admin\model\MkPayment as MkPaymentModel;
/**
 * MkOrder管理
 */
class MkOrder extends BaseController
{
    /**
     * 显示资源列表
     */
    public function index()
    {
        if ($this->request->isPost()) {
            $input = input("post.");
            $count = MkOrderModel::withSearch(["keyword"], $input)->count();
            // $data  = MkOrderModel::withSearch(["keyword"], $input)->order($input["prop"], $input["order"])->page($input["page"], $input["pageSize"])->select();
            $data  = MkOrderModel::alias('o')
			->field('o.*,p.name as paymentname,u.nickname')
			->join('mk_payment p','o.pid=p.id')
			->join('mk_user u','u.id=o.uid')
			->withSearch(["keyword"], $input)->order($input["prop"], $input["order"])->page($input["page"], $input["pageSize"])->select();
			foreach($data as $k=>$v){
				if($v->type==1){
					$v->types="数字货币";
				}elseif($v->type==2){
					$v->types="在线充值";
				}else{
					$v->types="信用卡充值";
				}
				$v->ordertime=date('Y-m-d H:i:s',$v->time);
				if($v->status==0){
					$v->statustext='<span style="color:#F56C6C">未支付</span>';
				}else{
					$v->statustext='<span style="color:#67C23A">已支付</span>';
				}
				if($v->time2){
					$v->paytime=date('Y-m-d H:i:s',$v->time2);
				}else{
					$v->paytime=null;
				}
			}
			return json(["status" => "success", "message" => "请求成功", "data" => $data, "count" => $count]);
        } else {
			$payment = MkPaymentModel::where('is_show', 1)->order('id', 'asc')->select();
            
			View::assign('payment', $payment);
			return View::fetch();
        }
    }
    
    /**
     * 保存新建的资源
     */
    public function save()
    {
        if ($this->request->isPost()) {
            MkOrderModel::create(input("post."));
            return json(["status" => "success", "message" => "添加成功"]);
        }
    }
    
    /**
     * 保存更新的资源
     */
    public function update()
    {
        if ($this->request->isPost()) {
            MkOrderModel::update(input("post."));
            return json(["status" => "success", "message" => "修改成功"]);
        }
    }
    
    /**
     * 删除指定资源
     */
    public function delete()
    {
        if ($this->request->isPost()) {
            MkOrderModel::destroy(input("post.ids"));
            return json(["status" => "success", "message" => "删除成功"]);
        }
    }
}