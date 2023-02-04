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
namespace plugins\withdrawal\admin\controller;

use think\facade\View;
use app\admin\BaseController;
use plugins\withdrawal\admin\model\MkWithdrawalSettings as MkWithdrawalSettingsModel;
use think\helper\Str;
/**
 * MkWithdrawalSettings管理
 */
class MkWithdrawalSettings extends BaseController
{
    /**
     * 显示资源列表
     */
    public function index()
    {
        if ($this->request->isPost()) {
            $input = input("post.");
            $count = MkWithdrawalSettingsModel::withSearch(["keyword"], $input)->count();
            $data  = MkWithdrawalSettingsModel::withSearch(["keyword"], $input)->order($input["prop"], $input["order"])->page($input["page"], $input["pageSize"])->select();
            $channel = null;
			$other = null;
			foreach($data as $k=>$v){
            	if($v->type==1){
            		$v->typename='数字货币';
            	}else{
            		$v->typename='在线提现';
            	}
				if($v->channelCode){
					$channelCode=json_decode($v->channelCode,true);
					foreach($channelCode as $key=>$value){
						$channel=$channel.$value.",";
					}
					$v->channelCode=Str::substr($channel, 0, Str::length($channel)-1);
				}
				if($v->other1){
					$other1=json_decode($v->other1,true);
					foreach($other1 as $key=>$value){
						$other=$other.$value."|";
					}
					$v->other1=Str::substr($other, 0, Str::length($other)-1);
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
			$input['update_time']=time();
			$input['add_time']=time();
            MkWithdrawalSettingsModel::create($input);
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
			$currency=explode(',',$input['currency']);
			$channelCode=explode(',',$input['channelCode']);
			$other=explode('|',$input['other1']);
			$channelarr;
			$otherarr;
			foreach($currency as $k=>$v){
				$channelarr[$v]=$channelCode[$k];
				if($k+1>count($other)){
					$otherarr[$v]="";
				}else{
					$otherarr[$v]=$other[$k];
				}
			}
			$input['channelCode']=json_encode($channelarr);
			$input['other1']=json_encode($otherarr);
			$input['update_time']=time();
            MkWithdrawalSettingsModel::update($input);
            return json(["status" => "success", "message" => "修改成功"]);
        }
    }
    
    /**
     * 删除指定资源
     */
    public function delete()
    {
        if ($this->request->isPost()) {
            MkWithdrawalSettingsModel::destroy(input("post.ids"));
            return json(["status" => "success", "message" => "删除成功"]);
        }
    }
}