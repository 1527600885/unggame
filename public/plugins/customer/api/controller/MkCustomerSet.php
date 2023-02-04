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
namespace plugins\customer\api\controller;

use app\api\BaseController;
// use app\admin\BaseController;
use plugins\customer\api\model\MkCustomerSet as MkCustomerSetModel;
use app\admin\model\Admin as AdminModel;
use plugins\customer\admin\model\MkCustomerLog as MkCustomerLogModel;
/**
 * MkCustomerSet管理
 */
class MkCustomerSet extends BaseController
{
	protected $noNeedLogin = ['index'];
    /**
     * 显示资源列表
     * 访问：www.unicgm.com/api/customer/mkCustomerSet/index
     */
    public function index()
    {
		$getdata=input('get.');
		$adminuserinfo=AdminModel::alias('a')
		->join('mk_customer_set s','a.account=s.admin_account')
		->where('a.account',$getdata['account'])
		->find();
        $logc=MkCustomerLogModel::alias('m')
		->join('mk_user u','m.to_id=u.id')
		->where('m.form_id',$adminuserinfo['id'])
		->select();
		$data['data']['mine']=[
			'username'=>$adminuserinfo->name,
			'id'=>$adminuserinfo->id,
			'status'=>'online',
			'sign'=>'',
			'avatar'=>'http://tp1.sinaimg.cn/1571889140/180/40030060651/1',
			];
		
		$this->laysuccess('加载成功',$data);
		// return json(['code'=>0,'msg'=>'加载成功','data'=>]);
		// if ($this->request->isPost()) {
        //     $input = input("post.");
        //     $page  = empty($input["page"]) ? 1 : $input["page"];
        //     $count = MkCustomerSetModel::count();
        //     $data  = MkCustomerSetModel::page($page, 10)->select();
        //     return json(["status" => "success", "message" => "请求成功", "data" => $data, "count" => $count]);
        // }
    }
    
    /**
     * 显示资源详情
     */
    public function single()
    {
        if ($this->request->isPost()) {
            MkCustomerSetModel::find(input("post.id"));
            return json(["status" => "success", "message" => "请求成功", "data" => $data]);
        }
    }
    
    /**
     * 保存新建的资源
     */
    public function save()
    {
        if ($this->request->isPost()) {
            MkCustomerSetModel::create(input("post."));
            return json(["status" => "success", "message" => "添加成功"]);
        }
    }
    
    /**
     * 保存更新的资源
     */
    public function update()
    {
        if ($this->request->isPost()) {
            MkCustomerSetModel::update(input("post."));
            return json(["status" => "success", "message" => "修改成功"]);
        }
    }
    
    /**
     * 删除指定资源
     */
    public function delete()
    {
        if ($this->request->isPost()) {
            MkCustomerSetModel::destroy(input("post.ids"));
            return json(["status" => "success", "message" => "删除成功"]);
        }
    }
    
    /**
     * 自定义更多方法...
     */
}