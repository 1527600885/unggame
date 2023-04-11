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
namespace plugins\announcement\admin\controller;

use think\facade\View;
use app\admin\BaseController;
use plugins\announcement\admin\model\MkAnnouncement as MkAnnouncementModel;
/**
 * MkAnnouncement管理
 */
class MkAnnouncement extends BaseController
{
    /**
     * 显示资源列表
     */
    public function index()
    {
        if ($this->request->isPost()) {
            $input = input("post.");
            $count = MkAnnouncementModel::withSearch(["keyword"], $input)->count();
            $data  = MkAnnouncementModel::withSearch(["keyword"], $input)->with("user")->order($input["prop"], $input["order"])->append(["type_text","status_text"])->page($input["page"], $input["pageSize"])->select();
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
            $save = input("post.");
//            if($save['user'] == 0){
//                $save['user_id'] = 0;
//            }
            MkAnnouncementModel::create($save);
            return json(["status" => "success", "message" => "添加成功"]);
        }
    }
    
    /**
     * 保存更新的资源
     */
    public function update()
    {
        if ($this->request->isPost()) {
            $update = input("post.");
//            if($update['user_id'] == 0){
//                $update['user_id'] == 0;
//            }
            unset($update['create_time']);
            MkAnnouncementModel::update($update);
            return json(["status" => "success", "message" => "修改成功"]);
        }
    }
    
    /**
     * 删除指定资源
     */
    public function delete()
    {
        if ($this->request->isPost()) {
            MkAnnouncementModel::destroy(input("post.ids"));
            return json(["status" => "success", "message" => "删除成功"]);
        }
    }
}