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
namespace plugins\payment\admin\controller;

use think\facade\View;
use app\admin\BaseController;
use plugins\payment\admin\model\MkPayment as MkPaymentModel;
/**
 * MkPayment管理
 */
class MkPayment extends BaseController
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
            $input = input("post.");
            $count = MkPaymentModel::withSearch(["keyword"], $input)->count();
            $data  = MkPaymentModel::withSearch(["keyword"], $input)->order($input["prop"], $input["order"])->page($input["page"], $input["pageSize"])->select();
            foreach($data as $k=>$v){
				if($v->type==1){
					$v->type_text="数字货币";
				}elseif($v->type==2){
					$v->type_text="在线支付";
				}else{
					$v->type_text="信用卡支付";
				}
				switch($v->country){
					case 'en-us':
					    $v->country_text="美国";
					    break;
					case 'en-id':
					    $v->country_text="印度尼西亚";
					    break;
					case 'en-my':
					    $v->country_text="马来西亚";
					    break;
					case 'ja-jp':
					    $v->country_text="日本";
					    break;
					case 'km-km':
					    $v->country_text="柬埔寨";
					    break;
					case 'ko-kr':
					    $v->country_text="韩国";
					    break;
					case 'th-th':
					    $v->country_text="泰国";
					    break;
					case 'vi-vn':
					    $v->country_text="越南";
					    break;
					default:
					    $v->country_text="全部";
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
			$channel=explode(',',$input['channel']);
			$currency_name=explode(',',$input['currency_name']);
			if(count($channel)!=count($currency_name)){
				return json(["status" => "error", "message" => "添加失败"]);
			}
			$channelarr;
			foreach($currency_name as $k=>$v){
				$channelarr[$v]=$channel[$k];
			}
			$input['channel']=json_encode($channelarr);
            MkPaymentModel::create($input);
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
			$input['update_time']=time();
			$channel=explode(',',$input['channel']);
			$currency_name=explode(',',$input['currency_name']);
			if(count($channel)!=count($currency_name)){
				return json(["status" => "error", "message" => "修改失败"]);
			}
			foreach($currency_name as $k=>$v){
				$channelarr[$v]=$channel[$k];
			}
			$input['channel']=json_encode($channelarr);
			foreach($this->lang as $v){
				unset($input[$v]);
			}
            MkPaymentModel::update($input);
            return json(["status" => "success", "message" => "修改成功"]);
        }
    }
    
    /**
     * 删除指定资源
     */
    public function delete()
    {
        if ($this->request->isPost()) {
            MkPaymentModel::destroy(input("post.ids"));
            return json(["status" => "success", "message" => "删除成功"]);
        }
    }
}