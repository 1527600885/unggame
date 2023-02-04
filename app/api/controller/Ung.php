<?php
declare (strict_types = 1);

namespace app\api\controller;
use app\api\BaseController;
use think\Request;
use think\facade\Cache;

class Ung extends BaseController
{
	protected $noNeedLogin = ['ungset','ungdata'];
	public function initialize(){
		parent::initialize();
		$this->UngSetModel = new \app\api\model\UngSet;//虚拟币设置
		$this->UngUserModel = new \app\api\model\UngUser;//用户持有虚拟币数量
		$this->UngUserLogModel = new \app\api\model\UngUserLog;//虚拟币日志
		$this->CapitalFlowModel = new \app\api\model\CapitalFlow;//资金流水
	}
	// 获取虚拟币的相关设置
	public function ungset(){
		$ungone=$this->UngSetModel->order('id asc')->find();
		// $this->lang
		$ungone->content=json_decode($ungone->content,true)[$this->lang];
		$this->success(lang('system.success'),$ungone);
	}
	// 获取相关的数据
	public function ungdata(){
		$userInfo=$this->nologuserinfo;
		$date=strtotime(date('Y-m-d 23:59:59'))-24*60*60;
		$time=time();
		if($userInfo){
			$data['amountdata']=$this->CapitalFlowModel->where(['uid'=>$userInfo['id'],'type'=>4,'money_type'=>1])->whereDay('add_time','yesterday')->sum('amount');
			$data['amountalldata']=$this->CapitalFlowModel->where(['uid'=>$userInfo['id'],'type'=>4,'money_type'=>1])->sum('amount');
		}else{
			$data['amountdata']=$this->CapitalFlowModel->where(['type'=>4,'money_type'=>1])->whereDay('add_time','yesterday')->sum('amount');
			$data['amountalldata']=$this->CapitalFlowModel->where(['type'=>4,'money_type'=>1])->sum('amount');
			if($data['amountdata']==0){
				// 虚假
			}
			if($data['amountalldata']==0){
				// 虚假
			}
		}
		// 活动用户-真实
		$usernum=$this->UngUserModel->where('num','>',0)->group('uid')->count();
		// 活动用户-虚假
		$cacheusernum=cache('usernum');
		if($cacheusernum){
			cache('usernum',$cacheusernum+rand(10,100));
		}else{
			$cacheusernum=rand(100,1000);
			cache('usernum',$cacheusernum);
		}
		$data['usernum']=$usernum+$cacheusernum;
		// 用户持有的总虚拟币数量-真实
		$ungnum=$this->UngUserModel->sum('num');
		// 用户持有的总虚拟币数量-虚假
		$cacheungnum=cache('ungnum');
		if($cacheungnum){
			cache('ungnum',$cacheungnum+rand(1,1));
		}else{
			$cacheungnum=rand(100,300);
			cache('ungnum',$cacheungnum);
		}
		$data['ungnum']=$ungnum+$cacheungnum;
		// 平台总运行天数
		$datenumarr=cache('datenum');
		if($datenumarr){
			if($datenumarr['time']<$date){
				$datenumarr['num']=$datenumarr['num']+1;
				$datenumarr['time']=$time;
				cache('datenum',$datenumarr);
			}
			$data['datenum']=$datenumarr['num'];
		}else{
			$datenumone=rand(10,100);
			cache('datenum',['num'=>$datenumone,'time'=>$time]);
			$data['datenum']=$datenumone;
		}
		$this->success(lang('system.success'),$data);
	}
	// 购买虚拟币
	public function buy(){
		
	}
	// 出售虚拟币
	public function sell(){
		
	}
	// 分红规则--由系统自动托管运行
	public function bonus(){
		
	}
}