<?php
declare (strict_types = 1);

namespace app\api\controller;
use app\api\BaseController;
use think\facade\Db;

use think\Request;
class Withdrawal extends BaseController
{
	protected $noNeedLogin = [];
	public function initialize(){
		parent::initialize();
		$this->WithdrawalSettingsModel = new \app\api\model\WithdrawalSettings;//提现设置相关
		$this->WithdrawalInfoModel = new \app\api\model\WithdrawalInfo;//用户提现信息
	}
	//查询提现的相关信息
	public function list(){
		$userInfo=$this->request->userInfo;
		$withdrawal_list=$this->WithdrawalSettingsModel->whereOr([
			[
				['is_show','=',1],
				['country','=','all']
			],
			[
				['is_show','=',1],
				['country','=',$this->lang]
			]
		])->order('id asc')->select();
		$data['list']=$withdrawal_list;
		$withdrawal_info=$this->WithdrawalInfoModel->where(['is_show'=>1,'uid'=>$userInfo['id']])->order('id asc')->select();
		foreach($withdrawal_list as $k=>$v){
			foreach($withdrawal_info as $key=>$value){
				if($v->id==$value->sid){
					$v->list=$value;
				}
			}
		}
		$data['info']=$withdrawal_list;
		$this->success(lang('system.success'),$data);
	}
}
?>