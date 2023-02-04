<?php
declare (strict_types = 1);

namespace app\api\controller;
use app\api\BaseController;
use think\facade\Db;

use think\Request;
class Payment extends BaseController
{
	protected $noNeedLogin = ['*'];
	public function initialize(){
		parent::initialize();
		$this->PaymentModel = new \app\api\model\Payment;//支付设置相关
		$this->CurrencyAllModel = new \app\api\model\CurrencyAll;//货币设置相关
	}
	
	public function getlist(){
		$data;
		$list=$this->PaymentModel->whereOr([
			[
				['is_show','=',1],
				['country','=','all']
			],[
				['is_show','=',1],
				['country','=',$this->lang]
			]
		])->order('id asc')->select();
		// echo  $this->PaymentModel->getLastSql();exit;
		$currency;
		foreach($list as $k=>$v){
			if($v->type==1){
				$data[$v->type-1]['payment_name']=lang('system.currency_name');
				$data[$v->type-1]['data'][$k]=$v;
				$v->rate=$this->CurrencyAllModel->where('pid',$v->id)->value('rate');
			}elseif($v->type==2){
				$data[$v->type-1]['payment_name']=lang('system.online_name');
				$data[$v->type-1]['data'][$k]=$v;
				$currency_name=explode(',',$v->currency_name);
				foreach($currency_name as $key=>$value){
					$currency[$key]['value']=$value;
					$currency[$key]['text']=$value;
					$currency[$key]['rate']=$this->CurrencyAllModel->where('name',$value)->value('rate');
				}
				$v->currency_name=$currency;
			}else{
				$data[$v->type-1]['payment_name']=lang('system.creditcards');
				$data[$v->type-1]['data'][$k]=$v;
			}
			//处理汇率
			// if($v->rate){
			// 	$rate=json_decode($v->rate,true);
			// 	$list[$k][$this->lang]=$rate[$this->lang];
			// }
			// unset($v->rate);
		}
		$this->success(lang('system.success'),$data);
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