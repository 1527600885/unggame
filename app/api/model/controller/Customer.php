<?php

namespace app\api\controller;

use think\Request;
use app\api\BaseController;

class Customer extends BaseController
{
	protected $noNeedLogin = [];
	public function initialize(){
		parent::initialize();
		$this->CustomerLogModel = new \app\api\model\MkCustomerLog;//聊天记录
		$this->CustomerSetModel = new \app\api\model\MkCustomerSet;//客服
		$this->CustomerPropagandaModel = new \app\api\model\MkCustomerPropaganda;//客服页面宣传语录
	}
	
	//获取客服相关的信息
	public function customerlist(){
		$data['userInfo']=$this->request->userInfo;
		//获取客服相关语录
		$data['Propaganda']=$this->CustomerPropagandaModel->where('status',1)->order('id asc')->select();
		//随机获取在线的客服信息
		$data['customer']=$this->CustomerSetModel->where('status',1)->whereNotNull('client_id')->extra('rand()')->find();
		
		$this->success(lang('system.success'),$data);
	}
}
