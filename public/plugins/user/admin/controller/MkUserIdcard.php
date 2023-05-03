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
namespace plugins\user\admin\controller;

use think\facade\View;
use app\admin\BaseController;
use plugins\user\admin\model\MkUserIdcard as MkUserIdcardModel;
/**
 * MkUserIdcard管理
 */
class MkUserIdcard extends BaseController
{
    /**
     * 显示资源列表
     */
    public function index()
    {
        if ($this->request->isPost()) {
            
            $input = input("post.");
            $count = MkUserIdcardModel::withSearch(["keyword"], $input)->count();
             
            $data  = MkUserIdcardModel::withSearch(["keyword"], $input)->append(["status_text"])->order($input["prop"], $input["order"])->page($input["page"], $input["pageSize"])->select()->each(function($item){
                $item['idCard_image'] = env('aws.imgurl').$item['idCard_image'];
                $item['idCard_image_with_hand'] = env('aws.imgurl').$item['idCard_image_with_hand'];
                return $item;
            });
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
            MkUserIdcardModel::create(input("post."));
            return json(["status" => "success", "message" => "添加成功"]);
        }
    }
    
    /**
     * 保存更新的资源
     */
    public function update()
    {
        if ($this->request->isPost()) {
            MkUserIdcardModel::update(input("post."));
            return json(["status" => "success", "message" => "修改成功"]);
        }
    }
    
    /**
     * 删除指定资源
     */
    public function delete()
    {
        if ($this->request->isPost()) {
            MkUserIdcardModel::destroy(input("post.ids"));
            return json(["status" => "success", "message" => "删除成功"]);
        }
    }
    public function check(){
        if ($this->request->isPost()) {
            $data = input("post.");
            $data['review_time'] = time();
            MkUserIdcardModel::update($data);
            return json(["status" => "success", "message" => "修改成功"]);
        }
    }
}