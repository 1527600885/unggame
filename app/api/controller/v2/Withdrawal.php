<?php


namespace app\api\controller\v2;


use app\admin\model\Config as ConfigModel;
use app\api\BaseController;
use app\api\model\CurrencyAll;
use app\api\model\GameBetLog;
use app\api\model\v2\UserIdcard;
use app\api\model\WithdrawalSettings;

class Withdrawal extends BaseController
{
    public function getWithdrawalInfo($type = "")
    {
        $userInfo=$this->request->userInfo;
        $useridcard = UserIdcard::where("user_id", $userInfo->id)->find();
        if(!$useridcard || $useridcard['status']!=1){
            $this->error('Real-name authentication required for account cash withdrawal.',"",412);
        }
        $withdrawConfig =  ConfigModel::getVal('withdraw');
        $rate = $this->getRate($withdrawConfig);
        $minPrice = $withdrawConfig['minprice'];
        $balance = $userInfo['balance'];
        $water = 0;
        if($type)
        {
            $water = round(GameBetLog::where(['user_id' => $userInfo->id])->whereDay('betTime')->sum('betAmount'), 2);
            $rateList = cacheRate();
            $water = bcmul($rateList[$type], $water, 8);
        }
        $this->success(lang('system.operation_succeeded'),compact("rate","minPrice","water","balance"));
    }
    /**
     * 获取后台配置的提现费率(要求邀请人数从小到大排列)
     * @return int
     */
    public function getRate($withdrawConfig)
    {

        $invite_nume = $this->request->userInfo["invite_one_num"];
        $rate = 0;
        foreach ($withdrawConfig['tableData'] as $v){
            if($invite_nume >= $v['number']){
                $rate = $v['rate'];
            }
        }
        return $rate;
    }
    public function getWithdrawalList()
    {
        $param = input("param.");
        $currency = CurrencyAll::where("is_show", 1)->cache("currency_all_show",600)->field("id,name,type,country,symbol,thumb_img,url_list,payment_ids,withdrawl_ids")->select()->toArray();
        $lists = array_column($currency,NULL,"name");
        $data = $lists[$param['type']];
        $withdrawalList = WithdrawalSettings::where("id","in",$data['withdrawl_ids'])->where("is_show",1)->field("id,name,image,min_amount,max_amount")->select();
        $currency_data = ['symbol'=>$data['symbol'],"thumb_img"=>$data['thumb_img']];
        $this->success(lang('system.operation_succeeded'),compact("withdrawalList","currency_data"));
    }
}