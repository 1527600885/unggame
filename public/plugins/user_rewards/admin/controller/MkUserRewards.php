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
namespace plugins\user_rewards\admin\controller;

use think\facade\View;
use app\admin\BaseController;
use plugins\user_rewards\admin\model\MkUserRewards as MkUserRewardsModel;
/**
 * MkUserRewards管理
 */
class MkUserRewards extends BaseController
{
    /**
     * 显示资源列表
     */
    public function index()
    {
        if ($this->request->isPost()) {
            $input = input("post.");
            $count = MkUserRewardsModel::withSearch(["keyword","status","date"], $input)->count();
            $data  = MkUserRewardsModel::withSearch(["keyword","status","date"], $input)->with(["user","rewards"])->append(["status_text"])->order($input["prop"], $input["order"])->page($input["page"], $input["pageSize"])->select();
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
            MkUserRewardsModel::create(input("post."));
            return json(["status" => "success", "message" => "添加成功"]);
        }
    }
    
    /**
     * 保存更新的资源
     */
    public function update()
    {
        if ($this->request->isPost()) {
            MkUserRewardsModel::update(input("post."));
            return json(["status" => "success", "message" => "修改成功"]);
        }
    }
    
    /**
     * 删除指定资源
     */
    public function delete()
    {
        if ($this->request->isPost()) {
            MkUserRewardsModel::destroy(input("post.ids"));
            return json(["status" => "success", "message" => "删除成功"]);
        }
    }
    public function check()
    {

    }
}