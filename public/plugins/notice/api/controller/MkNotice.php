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
namespace plugins\notice\api\controller;

use app\api\BaseController;
use plugins\notice\api\model\MkNotice as MkNoticeModel;
/**
 * MkNotice管理
 */
class MkNotice extends BaseController
{
    /**
     * 显示资源列表
     * 访问：game.uswindltd.com/api/notice/mkNotice/index
     */
    public function index()
    {
        if ($this->request->isPost()) {
            $input = input("post.");
            $page  = empty($input["page"]) ? 1 : $input["page"];
            $count = MkNoticeModel::count();
            $data  = MkNoticeModel::page($page, 10)->select();
            return json(["status" => "success", "message" => "请求成功", "data" => $data, "count" => $count]);
        }
    }
    
    /**
     * 显示资源详情
     */
    public function single()
    {
        if ($this->request->isPost()) {
            MkNoticeModel::find(input("post.id"));
            return json(["status" => "success", "message" => "请求成功", "data" => $data]);
        }
    }
    
    /**
     * 保存新建的资源
     */
    public function save()
    {
        if ($this->request->isPost()) {
            MkNoticeModel::create(input("post."));
            return json(["status" => "success", "message" => "添加成功"]);
        }
    }
    
    /**
     * 保存更新的资源
     */
    public function update()
    {
        if ($this->request->isPost()) {
            MkNoticeModel::update(input("post."));
            return json(["status" => "success", "message" => "修改成功"]);
        }
    }
    
    /**
     * 删除指定资源
     */
    public function delete()
    {
        if ($this->request->isPost()) {
            MkNoticeModel::destroy(input("post.ids"));
            return json(["status" => "success", "message" => "删除成功"]);
        }
    }
    
    /**
     * 自定义更多方法...
     */
}