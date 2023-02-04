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
namespace plugins\notice\admin\controller;

use think\facade\View;
use app\admin\BaseController;
use plugins\notice\admin\model\MkNotice as MkNoticeModel;
/**
 * MkNotice管理
 */
class MkNotice extends BaseController
{
	public function initialize(){
		parent::initialize();
		$this->lang=config('lang.allow_lang_list');
	}
    /**
     * 显示资源列表
     */
    public function index()
    {
        if ($this->request->isPost()) {
			// $lang=config('lang.allow_lang_list');
            $input = input("post.");
            $count = MkNoticeModel::withSearch(["keyword"], $input)->count();
            $data  = MkNoticeModel::withSearch(["keyword"], $input)->order($input["prop"], $input["order"])->page($input["page"], $input["pageSize"])->select();
            foreach($data as $k=>$v){
				if($v->content){
					$content=json_decode($v->content,true);
					foreach($this->lang as $key=>$value){
						if($content[$value]){
							$v->$value=$content[$value];
						}
					}
				}
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
			$content;
			foreach($this->lang as $key=>$value){
				$content[$value]=$input[$value];
				unset($input[$value]);
			}
			$input['content']=json_encode($content);
            MkNoticeModel::create($input);
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
			$content;
			foreach($this->lang as $key=>$value){
				$content[$value]=$input[$value];
				unset($input[$value]);
			}
			$input['content']=json_encode($content);
            MkNoticeModel::update($input);
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
}