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
use plugins\customer\admin\model\MkCustomerPropaganda as MkCustomerPropagandaModel;
/**
 * MkCustomerPropaganda管理
 */
class MkCustomerPropaganda extends BaseController
{
    /**
     * 显示资源列表
     */
    public function index()
    {
        if ($this->request->isPost()) {
            $input = input("post.");
            $count = MkCustomerPropagandaModel::withSearch(["keyword"], $input)->count();
            $data  = MkCustomerPropagandaModel::withSearch(["keyword"], $input)->order($input["prop"], $input["order"])->page($input["page"], $input["pageSize"])->select();
            foreach($data as $k=>$v){
				$v->update_time=date('Y-m-d H:i:s',$v->update_time);
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
			$input['update_time']=time();
			$input['add_time']=time();
            MkCustomerPropagandaModel::create($input);
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
			$input['update_time']=time();
            MkCustomerPropagandaModel::update($input);
            return json(["status" => "success", "message" => "修改成功"]);
        }
    }
    
    /**
     * 删除指定资源
     */
    public function delete()
    {
        if ($this->request->isPost()) {
            MkCustomerPropagandaModel::destroy(input("post.ids"));
            return json(["status" => "success", "message" => "删除成功"]);
        }
    }
}