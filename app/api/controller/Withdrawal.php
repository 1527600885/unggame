<?php
declare (strict_types = 1);

namespace app\api\controller;
use app\admin\model\Config as ConfigModel;
use app\api\BaseController;
use app\api\model\GameBetLog;
use think\facade\Db;

use think\Request;
class Withdrawal extends BaseController
{
	protected $noNeedLogin = [];
	public function initialize(){
		parent::initialize();
		$this->WithdrawalSettingsModel = new \app\api\model\WithdrawalSettings;//提现设置相关
		$this->WithdrawalInfoModel = new \app\api\model\WithdrawalInfo;//用户提现信息
		$this->WithdrawalModel = new \app\api\model\Withdrawal;//提现信息
		$this->CurrencyAllModel = new \app\api\model\CurrencyAll;//货币设置相关
		$this->UserModel = new \app\api\model\User;//用户信息模型
		$this->OrderModel = new \app\api\model\Order;//用户信息模型
	}
	//获取当前支持提现的货币(包括数字货币和在线货币)
	public function currencylist(){
		$data['userInfo']=$this->request->userInfo;
//		$data['feel']=$this->feel($this->request->userInfo['id']);
        $withdrawConfig =  ConfigModel::getVal('withdraw');
		$data['feel'] = $this->getRate($withdrawConfig['tableData']);
		$data['withdrawConfigTableData'] = $withdrawConfig['tableData'];
		// 数字货币
		$digital=$this->WithdrawalSettingsModel->field('`id`,`name` as currency,`type`')->where(['is_show'=>1,'type'=>1])->select();
		$digitalarr=[];
		foreach($digital as $k=>$v){
			$digitalarr[$k]['type']=$v->type;
			$currencys=explode('(',$v->currency);
			$digitalarr[$k]['id']=$v->id;
			$digitalarr[$k]['currency']=$currencys[0];
			$digitalarr[$k]['currencys']=$v->currency;
			$digitalarr[$k]['rate']=$this->CurrencyAllModel->where(['name'=>$currencys[0]])->value('rate');
		}
		$currency_data=[];
		$ol=$this->WithdrawalSettingsModel->field('`currency`,`type`')->where([['is_show','=',1],['type','<>',1]])->select();
		foreach($ol as $k=>$v){
			$currency_name=explode(',',$v->currency);
			foreach($currency_name as $key=>$value){
                $currency_data[] = ["type"=>$v->type,"currency"=>$value,"rate"=>$this->CurrencyAllModel->where(['name'=>$value])->value('rate')];
//				$currency_data[$key]['type']=$v->type;
//				$currency_data[$key]['currency']=$value;
//				$currency_data[$key]['rate']=$this->CurrencyAllModel->where(['name'=>$value])->value('rate');
			}
		}
		$currency_data=remove_duplicate($currency_data);
		$data['currency']=array_merge($digitalarr,$currency_data);
		$this->success(lang('system.success'),$data);
	}
	// 获取在线提现对应货币支持的服务商
	public function business(){
		$data=input("post.");
		$businesslist=$this->WithdrawalSettingsModel->field('`id`,`name`,`other1`')->where('currency','like','%'.$data['currency'].'%')->select();
		$this->success(lang('system.success'),$businesslist);
	}
	// 获取当前选择提现所需要提交的内容参数
	public function form_countent(){
		$data=input("post.");
		$content=$this->WithdrawalSettingsModel->field('`id`,`other1`,`name`')->where('id',$data['id'])->find();
		$other_content=json_decode($content->other1,true)[$data['currency']];
		$other_arr=explode(',',$other_content);
		$content->other1=$other_arr;
		$content->bankList = getBankList($data['currency'],$data['payment_name']);
		$this->success(lang('system.success'),$content);
	}
	//提交提现
	public function setwithdrawal_log(){
		$userInfo=$this->request->userInfo;
		$useridcard =  Db::name("user_idcard")->where("user_id", $userInfo->id)->find();
		if(!$useridcard || $useridcard['status']!=1){
			$this->error('Real-name authentication required for account cash withdrawal.',"",412);
		}
		$input=input("post.");
		$payment_name  = $input['payment_name'];
		$w_info=$this->setwithdrawal_info($input);
		if($w_info==false){
			$this->error('unknown error!');
		}
		$userone=$this->UserModel->where(['id'=>$userInfo->id,'pay_paasword'=>$input['pay_paasword']])->find();
		if(!$userone){
			$this->error('Your Payment Password is wrong!');
		}
		$withdrawConfig =  ConfigModel::getVal('withdraw');

		if((float)$input['amount'] < $withdrawConfig['minprice'] || (float)$input['amount'] > (float)$userInfo->balance){
			$this->error('Withdrawal amount error！');
		}
        if(!$this->isCanwithdrawal($userInfo->balance,$withdrawConfig['rate'])){
            $this->error("Cash withdrawals are only permitted when your total bets for the day exceed three times your current balance.");
        }
		$rate=$this->CurrencyAllModel->where(['name'=>$input['currency']])->value('rate');
//		$feel=$this->feel($userInfo->id);
        $feel = $this->getRate($withdrawConfig['tableData']);
		if($input['type']==1){
			$rateamount=round($input['amount']/$rate,7);
			$charge=round($rateamount*($feel/100),7);
			$money=bcadd($rateamount."",-$charge."",2);
			$data['name']=$input['name'];
			$data['address']=$input['address'];
		}else{
			$rateamount=round($input['amount']*$rate,2);
			$charge=round($rateamount*($feel/100),2);
			$money=bcadd($rateamount."",-$charge."",2);
			$splicedata=array_splice($input,7);
			$data['other']=json_encode($splicedata);
		}
		$data['sid']=$input['id'];
		$data['uid']=$userInfo->id;
		$data['type']=$input['type'];
		$data['currency']=$input['currency'];
		$data['amount']=$input['amount'];
		$data['payment_name'] = $payment_name;
		$data['money']=$money;
		$data['charge']=$charge;
		$data['add_time']=time();
		$Wid=$this->WithdrawalModel->insertGetId($data);
		if($Wid){
			$amount=$input['amount'];
			$userbalance=bcadd($userInfo->balance."",-$input['amount']."",2);
			$content='{withdrawal.text}{capital.money}'.$amount;
			$admin_content='用户'.$userInfo->nickname.'提现'.$amount.'美元';
			capital_flow($userInfo->id,$Wid,2,2,$amount,$userbalance,$content,$admin_content);
			$this->UserModel->where('id',$userInfo->id)->update(['balance'=>$userbalance]);
			$this->success(lang('system.success'));
		}else{
			$this->success(lang('system.id'));
		}
	}

    /**
     * 判断是否满足流水
     * @param $price 金额
     * @param int $rate 倍数
     * @return bool
     */
    public function isCanwithdrawal($price, $rate=3)
    {
        $user_id = $this->request->userInfo['id'];
        //统计今天的流水
        $today_bet = GameBetLog::where("user_id",$user_id)->whereTime("betTime","today")->sum("betAmount");
        if($today_bet >= $price*$rate)
        {
            return true;
        }else{
            return false;
        }
    }
	//增加用户的提现信息
	public function setwithdrawal_info($postdata){
		$userInfo=$this->request->userInfo;
		if($postdata['type']==1){
			$where=[
				'sid'=>$postdata['id'],
				'uid'=>$userInfo->id,
				'currency'=>$postdata['currency'],
			];
			$data=[
				'sid'=>$postdata['id'],
				'uid'=>$userInfo->id,
				'type'=>$postdata['type'],
				'currency'=>$postdata['currency'],
				'name'=>$postdata['name'],
				'address'=>$postdata['address'],
				'add_time'=>time()
			];
		}else{
			$where=[
				'sid'=>$postdata['id'],
				'uid'=>$userInfo->id,
				'currency'=>$postdata['currency'],
			];
			$data=[
				'sid'=>$postdata['id'],
				'uid'=>$userInfo->id,
				'type'=>$postdata['type'],
				'currency'=>$postdata['currency'],
				'add_time'=>time()
			];
			$splicedata=array_splice($postdata,7);
			$data['other']=json_encode($splicedata);
		}
		$withdrawalinfo=$this->WithdrawalInfoModel->where($where)->find();
		if($withdrawalinfo){
			$this->WithdrawalInfoModel->where($where)->update($data);
			return true;
		}else{
			$wid=$this->WithdrawalInfoModel->insertGetId($data);
			if($wid){
				return true;
			}else{
				return false;
			}
		}
	}
	// 获取对应的提现用户信息
	public function getwithdrawal_info(){
		$postdata=input("post.");
		$userInfo=$this->request->userInfo;
		$data['info']=null;
		$data['name']=null;
		$data['address']=null;
		if($postdata['type']==1){
			$infoone=$this->WithdrawalInfoModel->field('`name`,`address`')->where(['sid'=>$postdata['id'],'currency'=>$postdata['currency'],'uid'=>$userInfo->id])->find();
			if($infoone){
				$data['name']=$infoone->name;
				$data['address']=$infoone->address;
			}
		}else{
			$infoone=$this->WithdrawalInfoModel->field('`other`')->where(['sid'=>$postdata['id'],'currency'=>$postdata['currency'],'uid'=>$userInfo->id])->find();
			if($infoone){
				$data['info']=json_decode($infoone->other,true);
			}
		}
		$this->success(lang('system.success'),$data);
	}
	// 获取用户的提现手续费
	public function feel($uid){
		if($uid){
			// 用户直接邀请的朋友数量
			$invite_list=$this->UserModel->where('invite_one_uid',$uid)->select();
			$invite_count=count($invite_list);
			$invite_str=null;
			$money_sum=0;
			if($invite_count>0){
				foreach($invite_list as $k=>$v){
					if($k+1==$invite_count){
						$invite_str.=$v->id;
					}else{
						$invite_str.=$v->id.",";
					}
				}
				$money_sum=$this->OrderModel->where('status',1)->whereIn('uid',$invite_str)->sum('money');
			}

			if($invite_count<3){
				$feel=30;
			}elseif($invite_count>=3 && $invite_count<10){
				$feel=25;
			}elseif($invite_count>=10 && $invite_count<30){
				if($money_sum>=500){
					$feel=20;
				}else{
					$feel=25;
				}
			}else{
				if($money_sum>=5000){
					$feel=15;
				}else{
					$feel=25;
				}
			}
		}
		return $feel;
	}

    /**
     * 获取后台配置的提现费率(要求邀请人数从小到大排列)
     * @param $data
     * @return int
     */
    public function getRate($data)
    {
        $invite_nume = $this->request->userInfo["invite_one_num"];
        $rate = 0;
        foreach ($data as $v){
            if($invite_nume >= $v['number']){
                $rate = $v['rate'];
            }
        }
        return $rate;
    }
    public function getWithdrawLog()
    {
        $type = $this->request->post("type",0);
        $data = \app\api\model\Withdrawal::withSearch(["type","uid"],["type"=>$type,"uid"=>$this->request->userInfo['id']])->append(["typeText","add_time_text","pay_time_text",'online_status_text'])->order("id desc")->paginate();
        $this->success(lang('system.success'),$data);
    }
}
?>