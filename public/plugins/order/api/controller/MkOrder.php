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
namespace plugins\order\api\controller;

use app\api\BaseController;
use plugins\order\api\model\MkOrder as MkOrderModel;
/**
 * MkOrder管理
 */
class MkOrder extends BaseController
{
    /**
     * 显示资源列表
     * 访问：game.uswindltd.com/api/order/mkOrder/index
     */
    public function index()
    {
        if ($this->request->isPost()) {
            $input = input("post.");
            $page  = empty($input["page"]) ? 1 : $input["page"];
            $count = MkOrderModel::count();
            $data  = MkOrderModel::page($page, 10)->select();
            return json(["status" => "success", "message" => "请求成功", "data" => $data, "count" => $count]);
        }
    }
    
    /**
     * 显示资源详情
     */
    public function single()
    {
        if ($this->request->isPost()) {
            MkOrderModel::find(input("post.id"));
            return json(["status" => "success", "message" => "请求成功", "data" => $data]);
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
    
    /**
     * 自定义更多方法...
     */
}