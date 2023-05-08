<?php


namespace app\api\controller\v2;


use app\admin\model\Config as ConfigModel;
use app\api\BaseController;
use app\api\model\CurrencyAll;
use app\api\model\GameBetLog;
use app\api\model\v2\UserIdcard;
use app\api\model\v2\WithdrawalSettings;
use think\facade\Db;

class Withdrawal extends BaseController
{
    public function initialize(){
        parent::initialize();
        $this->WithdrawalSettingsModel = new \app\api\model\WithdrawalSettings;//提现设置相关
        $this->WithdrawalInfoModel = new \app\api\model\WithdrawalInfo;//用户提现信息
        $this->WithdrawalModel = new \app\api\model\Withdrawal;//提现信息
        $this->CurrencyAllModel = new \app\api\model\CurrencyAll;//货币设置相关
        $this->UserModel = new \app\api\model\User;//用户信息模型
        $this->OrderModel = new \app\api\model\Order;//用户信息模型
    }
    public function getWithdrawalInfo($type = "")
    {
        $userInfo = $this->request->userInfo;
        $useridcard = UserIdcard::where("user_id", $userInfo->id)->find();
        if (!$useridcard || $useridcard['status'] != 1) {
            $this->error('Real-name authentication required for account cash withdrawal.', "", 412);
        }
        $withdrawConfig = ConfigModel::getVal('withdraw');
        $rate = $this->getRate($withdrawConfig);
        $minPrice = $withdrawConfig['minprice'];
        $balance = $userInfo['balance'];
        $water = 0;
        if ($type) {
            $water = round(GameBetLog::where(['user_id' => $userInfo->id])->whereDay('betTime')->sum('betAmount'), 2);
            $rateList = cacheRate();
            $water = bcmul($rateList[$type], $water, 8);
        }
        $this->success(lang('system.operation_succeeded'), compact("rate", "minPrice", "water", "balance"));
    }

    /**
     * 获取后台配置的提现费率(要求邀请人数从小到大排列)
     * @return int
     */
    public function getRate($withdrawConfig)
    {

        $invite_nume = $this->request->userInfo["invite_one_num"];
        $rate = 0;
        foreach ($withdrawConfig['tableData'] as $v) {
            if ($invite_nume >= $v['number']) {
                $rate = $v['rate'];
            }
        }
        return $rate;
    }

    public function getWithdrawalList()
    {
        $param = input("param.");
        $data = CurrencyAll::getDataByName($param['type']);
        $withdrawalList = WithdrawalSettings::where("id", "in", $data['withdrawl_ids'])->where("is_show", 1)->field("id,name,image,min_amount,max_amount")->select();
        $currency_data = ['symbol' => $data['symbol'], "thumb_img" => $data['thumb_img']];
        $this->success(lang('system.operation_succeeded'), compact("withdrawalList", "currency_data"));
    }

    public function getWithdrawalDetail($settings_id, $type)
    {
        $setting = WithdrawalSettings::where("id",$settings_id)->field("id,other1,ischoosebank,name")->find();
        $rateList = cacheRate();
        $min_amount =  bcmul(ConfigModel::getVal('withdraw')['minprice'],$rateList[$type],2);
        $max_amount = bcmul($this->request->userInfo['balance'],$rateList[$type],2);
        $setting['others'] = [];
        if($setting['other1'] && isset($setting['other1'][$type]))
        {
            $setting['others'] = explode(",",$setting['other1'][$type]);
            unset($setting['other1']);
        }
        $bankList = [];
        if($setting['ischoosebank'])
        {
            $bankList = getBankList($type,$setting['name']);
            unset($setting['others']['bank']);
        }
        $data = CurrencyAll::getDataByName($type);
        $thumb_img = $data['thumb_img'];
        $this->success(lang('system.operation_succeeded'), compact("setting", "thumb_img","min_amount","max_amount"));
    }
    //提交提现
    public function setwithdrawal_log(){
        $userInfo=$this->request->userInfo;
        $useridcard =  Db::name("user_idcard")->where("user_id", $userInfo->id)->find();
        if(!$useridcard || $useridcard['status']!=1){
            $this->error('Real-name authentication required for account cash withdrawal.',"",412);
        }
        $input=input("post.");
        if(isset($input['method']) && $input['method'] == 2)
        {
            $rate = getCoinMarketCap("USD",$input['currency']);
            $input['amount'] = bcmul($input['amount']."",$rate."",8);
        }else{
            $rate = cacheRate()[$input['currency']];
            $input['amount'] = bcdiv($input['amount']."",$rate,8);
        }
        $payment_name  = $input['payment_name'] ?? \app\api\model\WithdrawalSettings::where("id",$input['id'])->value("name");
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
        $feel = $this->getRate($withdrawConfig);
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
            $data['other'] = json_encode($input['other']);
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
}