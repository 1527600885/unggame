<?php


namespace app\api\controller\v2;


use app\admin\model\Config as ConfigModel;
use app\api\BaseController;
use app\api\model\GameBetLog;
use app\api\model\v2\UserIdcard;

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
            $rate = cacheRate();
            $water = bcmul($rate[$type], $water, 8);
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
}