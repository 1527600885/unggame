<?php
declare (strict_types = 1);

namespace app\api\controller;
use app\api\BaseController;
use think\facade\Db;

use think\Request;
class Payment extends BaseController
{
	protected $noNeedLogin = ['getone','currency','digital'];
	public function initialize(){
		parent::initialize();
		$this->PaymentModel = new \app\api\model\Payment;//支付设置相关
		$this->CurrencyAllModel = new \app\api\model\CurrencyAll;//货币设置相关
		$this->paymenttype=[lang('recharge.digital'),lang('recharge.cash'),lang('recharge.Credit')];
	}
	
	// 获取当前有的支付方式
	public function getpaymenttype(){
		$type=$this->PaymentModel->field('type')->where('is_show',1)->group('type')->order('type asc')->select();
		foreach($type as $k=>$v){
			$v->typename=$this->paymenttype[$v->type-1];
		}
		$this->success(lang('system.success'),$type);
	}
	
	public function getlist(){
		$type=input("post.type/d");
		$currency_data=[];
		if($type==1){
			$field='`id`,`name`,`type`,`url`';
		}else{
			$field='`id`,`name`,`currency_name`,`type`';
		}
		$list=$this->PaymentModel->field($field)->where(['is_show'=>1,'type'=>$type])->order('id asc')->select();
		foreach($list as $k=>$v){
			if($type==1){
				$name=explode("(",$v->name)[0];
				$v->nickname=$name;
				$v->rate=$this->CurrencyAllModel->where('name',$name)->value('rate');
			}else{
				$currency_name=explode(',',$v->currency_name);
				foreach($currency_name as $key=>$value){
					if(count($currency_data)<=0){
						$currency_data[$key]['name']=$value;
						$currency_data[$key]['type']=$type;
						$currency_data[$key]['rate']=$this->CurrencyAllModel->where('name',$value)->value('rate');
					}else{
						foreach($currency_data as $ke=>$va){
							if($value!=$va['name']){
								$currency_data[$key]['name']=$value;
								$currency_data[$key]['type']=$type;
								$currency_data[$key]['rate']=$this->CurrencyAllModel->where('name',$value)->value('rate');
							}
						}
					}
				}
				$v->currency_name=$currency_data;
			}
		}
		if($type!=1){
			$list=$currency_data;
		}
		$this->success(lang('system.success'),$list);
	}
	// 查询在线充值是否支持当前货币的充值
	public function business(){
		$currency=input("post.currency");
		$list=$this->PaymentModel->field('`id`,`name`,`type`,`logo`')->where([
			['is_show','=',1],
			['currency_name','like','%'.$currency.'%']
		])->select();
		$this->success(lang('system.success'),$list);
	}
	public function getone(){
		$id=input('post.id/d');
		$paymentinfo=$this->PaymentModel->where('id',$id)->find();
		$paymentinfo->rate=json_decode($paymentinfo->rate,true)[$this->lang];
		if($paymentinfo){
			$this->success(lang('system.success'),$paymentinfo);
		}else{
			$this->error(lang('system.success'));
		}
	}
	
	//货币汇率
	public function currency(){
		$usd=$this->curlHttp("https://data.mifengcha.com/api/v3/exchange_rate?api_key=Y9ICFN44GDQBODG8CVWGCAXIQXQJWE3U6RZTQBO1");
		$data;
		// foreach($usd as $k=>$v){
		// 	$data[$k]['type']=2;
		// 	$data[$k]['name']=$v['c'];
		// 	$data[$k]['rate']=(string)$v['r'];
		// 	$data[$k]['add_time']=time();
		// }
		// $this->CurrencyAllModel->insertAll($data);
		// return json_encode($data);
	}
	//数字货币兑换美元的汇率,再以美元转换成对应货币的汇率
	public function digital(){
		$usdt=$this->curlHttp("https://data.mifengcha.com/api/v3/price?slug=bitcoin,tether,ethereum&api_key=Y9ICFN44GDQBODG8CVWGCAXIQXQJWE3U6RZTQBO1");
		$data;
		// foreach($usdt as $k=>$v){
		// 	$data[$k]['type']=1;
		// 	$data[$k]['name']=$v['S'];
		// 	$data[$k]['other_name']=$v['S'];
		// 	$data[$k]['rate']=(string)$v['u'];
		// 	$data[$k]['add_time']=time();
		// 	if($v['S']=='BTC'){
		// 		$data[$k]['pid']=2;
		// 	}elseif($v['S']=='ETH'){
		// 		$data[$k]['pid']=3;
		// 	}elseif($v['S']=='USDT'){
		// 		$data[$k]['pid']=1;
		// 	}
		// }
		// $this->CurrencyAllModel->insertAll($data);
	}
	
	
	public function curlHttp($url){
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL,$url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HEADER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		$output = curl_exec($ch);
		//释放curl句柄
		curl_close($ch);
		return json_decode($output,true);
	}
}
?>