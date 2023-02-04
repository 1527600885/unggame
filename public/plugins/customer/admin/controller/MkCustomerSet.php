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
namespace plugins\customer\admin\controller;

use think\facade\View;
use app\admin\BaseController;
use plugins\customer\admin\model\MkCustomerSet as MkCustomerSetModel;
/**
 * MkCustomerSet管理
 */
class MkCustomerSet extends BaseController
{
    /**
     * 显示资源列表
     */
    public function index()
    {
        if ($this->request->isPost()) {
            $input = input("post.");
            $count = MkCustomerSetModel::withSearch(["keyword"], $input)->count();
            $data  = MkCustomerSetModel::withSearch(["keyword"], $input)->order($input["prop"], $input["order"])->page($input["page"], $input["pageSize"])->select();
            foreach($data as $k=>$v){
            	if($v->online){
            		$v->online=date('Y-m-d H:i:s',$v->online);
            	}
            	if($v->outline){
            		$v->outline=date('Y-m-d H:i:s',$v->outline);
            	}
            	$v->add_time=date('Y-m-d H:i:s',$v->add_time);
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
			$input=input("post.");
			if($input['status']==0){
				$input['outline']=time();
			}else{
				$input['online']=time();
			}
			$input['add_time']=time();
            MkCustomerSetModel::create($input);
            return json(["status" => "success", "message" => "添加成功"]);
        }
    }
    
    /**
     * 保存更新的资源
     */
    public function update()
    {
        if ($this->request->isPost()) {
			$input=input("post.");
			if($input['status']==0){
				$input['outline']=time();
				unset($input['online']);
			}else{
				$input['online']=time();
				unset($input['outline']);
			}
			unset($input['add_time']);
            MkCustomerSetModel::update($input);
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
	 * 聊天相关信息获取
	 */
	public function init(){
		dd(session('admin'));
	}
}