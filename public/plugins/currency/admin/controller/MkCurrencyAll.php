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
namespace plugins\currency\admin\controller;

use app\common\lib\Redis;
use think\facade\View;
use app\admin\BaseController;
use plugins\currency\admin\model\MkCurrencyAll as MkCurrencyAllModel;
/**
 * MkCurrencyAll管理
 */
class MkCurrencyAll extends BaseController
{
    /**
     * 显示资源列表
     */
    public function index()
    {
        if ($this->request->isPost()) {
            $input = input("post.");
            $count = MkCurrencyAllModel::withSearch(["keyword"], $input)->where("is_show",1)->count();
            $data  = MkCurrencyAllModel::withSearch(["keyword"], $input)->where("is_show",1)->order($input["prop"], $input["order"])->page($input["page"], $input["pageSize"])->select();
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
            MkCurrencyAllModel::create(input("post."));
            return json(["status" => "success", "message" => "添加成功"]);
        }
    }
    
    /**
     * 保存更新的资源
     */
    public function update()
    {
        if ($this->request->isPost()) {
            $data= input("post.");
            $redis = (new Redis())->getRedis();
            $redis->del("currency_all_show");
            if(isset($data['payment_ids'])) unset($data['payment_ids']);
            MkCurrencyAllModel::update($data);
            return json(["status" => "success", "message" => "修改成功"]);
        }
    }
    
    /**
     * 删除指定资源
     */
    public function delete()
    {
        if ($this->request->isPost()) {
            MkCurrencyAllModel::destroy(input("post.ids"));
            return json(["status" => "success", "message" => "删除成功"]);
        }
    }
}