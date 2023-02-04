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
use plugins\ung\admin\model\MkUngSet as MkUngSetModel;
/**
 * MkUngSet管理
 */
class MkUngSet extends BaseController
{
    /**
     * 显示资源列表
     */
    public function index()
    {
        if ($this->request->isPost()) {
            $input = input("post.");
            $count = MkUngSetModel::withSearch(["keyword"], $input)->count();
            $data  = MkUngSetModel::withSearch(["keyword"], $input)->order($input["prop"], $input["order"])->page($input["page"], $input["pageSize"])->select();
            foreach($data as $k=>$v){
				$content=json_decode($v->content,true);
				foreach($content as $key=>$value){
					$v->$key=$value;
				}
				$v->interests=$v->interest."%";
				$v->update_times=date('Y-m-d H:i:s',$v->update_time);
				$v->add_times=date('Y-m-d H:i:s',$v->add_time);
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
			$input['content']=[];
			if($input['en-us']){
				$input['content']['en-us']=$input['en-us'];
				unset($input['en-us']);
			}
			if($input['en-id']){
				$input['content']['en-id']=$input['en-id'];
				unset($input['en-id']);
			}
			if($input['en-my']){
				$input['content']['en-my']=$input['en-my'];
				unset($input['en-my']);
			}
			if($input['ja-jp']){
				$input['content']['ja-jp']=$input['ja-jp'];
				unset($input['ja-jp']);
			}
			if($input['km-km']){
				$input['content']['km-km']=$input['km-km'];
				unset($input['km-km']);
			}
			if($input['ko-kr']){
				$input['content']['ko-kr']=$input['ko-kr'];
				unset($input['ko-kr']);
			}
			if($input['th-th']){
				$input['content']['th-th']=$input['th-th'];
				unset($input['th-th']);
			}
			if($input['vi-vn']){
				$input['content']['vi-vn']=$input['vi-vn'];
				unset($input['vi-vn']);
			}
			$input['content']=json_encode($input['content']);
			$input['update_time']=time();
			$input['add_time']=time();
            MkUngSetModel::create($input);
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
			$input['content']=[];
			if($input['en-us']){
				$input['content']['en-us']=$input['en-us'];
				unset($input['en-us']);
			}
			if($input['en-id']){
				$input['content']['en-id']=$input['en-id'];
				unset($input['en-id']);
			}
			if($input['en-my']){
				$input['content']['en-my']=$input['en-my'];
				unset($input['en-my']);
			}
			if($input['ja-jp']){
				$input['content']['ja-jp']=$input['ja-jp'];
				unset($input['ja-jp']);
			}
			if($input['km-km']){
				$input['content']['km-km']=$input['km-km'];
				unset($input['km-km']);
			}
			if($input['ko-kr']){
				$input['content']['ko-kr']=$input['ko-kr'];
				unset($input['ko-kr']);
			}
			if($input['th-th']){
				$input['content']['th-th']=$input['th-th'];
				unset($input['th-th']);
			}
			if($input['vi-vn']){
				$input['content']['vi-vn']=$input['vi-vn'];
				unset($input['vi-vn']);
			}
			$input['content']=json_encode($input['content']);
			$input['update_time']=time();
            MkUngSetModel::update($input);
            return json(["status" => "success", "message" => "修改成功"]);
        }
    }
    
    /**
     * 删除指定资源
     */
    public function delete()
    {
        if ($this->request->isPost()) {
            MkUngSetModel::destroy(input("post.ids"));
            return json(["status" => "success", "message" => "删除成功"]);
        }
    }
}