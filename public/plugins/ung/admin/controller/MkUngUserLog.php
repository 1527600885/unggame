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
use think\Facade\Db;
use app\admin\BaseController;
use plugins\ung\admin\model\MkUngUserLog as MkUngUserLogModel;
/**
 * MkUngUserLog管理
 */
class MkUngUserLog extends BaseController
{
    /**
     * 显示资源列表
     */
    public function index()
    {
        if ($this->request->isPost()) {
            $input = input("post.");
            $count = MkUngUserLogModel::withSearch(["keyword"], $input)->count();
            $data  = MkUngUserLogModel::withSearch(["keyword"], $input)->order($input["prop"], $input["order"])->page($input["page"], $input["pageSize"])->select()->each(function($item){
                    $info=Db::name("user")->field('id,nickname')->where('id',$item["uid"])->select()->toArray();
                    $infos=Db::name("user")->field('id,nickname')->where('id',$item["touserid"])->select()->toArray();
                    $item['nickname']=$info[0]['nickname'];
                    $item['tonickname']=$infos[0]['nickname'];
                    return $item;
            });
            foreach ($data as $k=>$value) {
                switch ($value->type)
                {
                    case 1:
                        $value->type="转账";
                        break;
                    case 2:
                        $value->type="官方转账";
                        break;
                    case 3:
                        $value->type="申购";
                        break;
                    case 4:
                        $value->type="赎回";
                        break;
                    default:
                        $value->type="";
                }
                $value->add_times = date("Y-m-d H:i:s",$value->add_time);    
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
            MkUngUserLogModel::create(input("post."));
            return json(["status" => "success", "message" => "添加成功"]);
        }
    }
    
    /**
     * 保存更新的资源
     */
    public function update()
    {
        if ($this->request->isPost()) {
            MkUngUserLogModel::update(input("post."));
            return json(["status" => "success", "message" => "修改成功"]);
        }
    }
    
    /**
     * 删除指定资源
     */
    public function delete()
    {
        if ($this->request->isPost()) {
            MkUngUserLogModel::destroy(input("post.ids"));
            return json(["status" => "success", "message" => "删除成功"]);
        }
    }
}