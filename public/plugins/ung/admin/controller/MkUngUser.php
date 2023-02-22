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
namespace plugins\ung\admin\controller;

use think\facade\View;
use app\admin\BaseController;
use think\Facade\Db;
use plugins\ung\admin\model\MkUngUser as MkUngUserModel;
/**
 * MkUngUser管理
 */
class MkUngUser extends BaseController
{
    /**
     * 显示资源列表
     */
    public function index()
    {
        if ($this->request->isPost()) {
            $input = input("post.");
            $count = MkUngUserModel::withSearch(["keyword"], $input)->count();
            $data  = MkUngUserModel::withSearch(["keyword"], $input)->order($input["prop"], $input["order"])->page($input["page"], $input["pageSize"])->select()->each(function($item){
                    $info=Db::name("user")->field('id,nickname')->where('id',$item["uid"])->select()->toArray();
                    $item['nickname']=$info[0]['nickname'];
                    $moneyxl = Db::name("ung_set")->field("price")->value("price");
                    $item["money"] = bcmul($item['num'],$moneyxl,2);
                    return $item;
            });;
            foreach ($data as $k=>$value) {
                $value->update_time = date("Y-m-d H:i:s",$value->update_time);
                $value->add_time = date("Y-m-d H:i:s",$value->add_time);     
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
            MkUngUserModel::create(input("post."));
            return json(["status" => "success", "message" => "添加成功"]);
        }
    }
    
    /**
     * 保存更新的资源
     */
    public function update()
    {
        if ($this->request->isPost()) {
            MkUngUserModel::update(input("post."));
            return json(["status" => "success", "message" => "修改成功"]);
        }
    }
    
    /**
     * 删除指定资源
     */
    public function delete()
    {
        if ($this->request->isPost()) {
            MkUngUserModel::destroy(input("post.ids"));
            return json(["status" => "success", "message" => "删除成功"]);
        }
    }
}