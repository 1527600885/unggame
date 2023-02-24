<?php


namespace app\api\controller;


use app\api\BaseController;
use app\api\model\GameList;
use app\api\model\UserSign as UserSignModel;
use think\facade\Db;

class UserSign extends BaseController
{
    public function index()
    {
        $data = [
            "todayReward"=>20,
            "tomorrowReward"=>20,
            "platformReward"=>300
        ];
        $user_id = $this->request->userInfo['id'];
        $model = new UserSignModel();
        //查询今天是否签到过
        $last = $model->where("user_id",$user_id)
            ->order("id desc")
            ->find();
        $data['totalSignDay'] = $this->request->userInfo['days'];//连续签到天数
        //isday今日奖励,islx连续签到奖励,isplat平台奖励, 0=>可领取,1=>不可领取
        if($last)
        {
            if($last['last_sign_time']+3600 > time())
            {
                //一小时内签到过
                $data['isday'] = 1;
                $data['islx'] = 1;
                $data['isplat'] = 1;
            }else if($last['last_sign_time'] > strtotime(date("Ymd 00:00:00"))){

                //当日签到过
                $data['isday'] = 1;
                $data['islx'] = 1;
                $data['isplat'] = 0;
            }else if($last['last_sign_time'] > strtotime(date("Ymd 00:00:00"))-24*3600){
                //昨天签到过
                $data['isday'] = 0;
                $data['islx'] = 0;
                $data['isplat'] = 0;
            }else{
                //签到时间在昨天之前
                $data['isday'] = 0;
                $data['islx'] = 1;
                $data['isplat'] = 0;
                $data['totalSignDay'] = 0;
            }
        }else{
            //从未签过到
            $data['isday'] = 0;
            $data['islx'] = 1;
            $data['isplat'] = 0;
        }
        $data['totalReward'] = $this->request->userInfo['total_rewards'];
        if(!$last){
            $signTime =  strtotime(date("Ymd H:00:00"))+3600-time();
        }else{
            $signTime = $last['last_sign_time']+3600 - time();
        }

        $hour = 0;
        $minute = intval($signTime/60);
        $second = $signTime%60;
        $times  = compact("hour","minute","second");
        $hot_where=[
            ['displayStatus','=',1],
            ['is_groom','=',1],//是否首页推荐
            ['groom_sort','>',0],//排序序列号>0
        ];
        $hot_order='groom_sort asc,hot desc';//排序号排序
        $hot_game = GameList::withoutField('add_time')->where($hot_where)->limit(4)->order($hot_order)->select();
        foreach($hot_game as $k=>$v){
            $gameName=json_decode($v->gameName,true);//游戏名称字段
            $v->gameName=croppstring($gameName[$this->gamelang],6);//gamelang:游戏语言

            $gameImage=json_decode($v->gameImage,true);//游戏图片
            if($this->gamelang=='KO'){
                $v->gameImage=$gameImage['EN'];
            }elseif($this->gamelang=='MS'){
                $v->gameImage=$gameImage['ID'];
            }else{
                $v->gameImage=$gameImage[$this->gamelang];
            }
            $v->type="heart";
        }
        $this->success("获取成功",['rewardData'=>$data,"times"=>$times,"gamelist"=>$hot_game]);

    }
    public function signIn()
    {
        $user_id = $this->request->userInfo['id'];
        $model = new UserSignModel();
        //查询今天是否签到过
       $last = $model->where("user_id",$user_id)
            ->whereTime("last_sign_time","today")
            ->order("id desc")
            ->find();
       $platRewards = 300;//平台奖励
       if(!$last)
       {
           $todayRewards  = 0;
           $dayRewards = 20;//签到奖励
           $lxRewards = 20;//连续签到奖励
           //今天未签到,首先获取签到奖励
           $data[] = ["last_sign_time"=>time(),"rewards"=>$dayRewards,"type"=>1,"user_id"=>$user_id];
           //获取当前时间段的平台奖励
           $data[] = ["last_sign_time"=>time(),"rewards"=>$platRewards,"type"=>3,"user_id"=>$user_id];
           $todayRewards = bcadd(bcadd($todayRewards , $dayRewards,2) , $platRewards,2);//今天签到获得奖励
           //查询昨天是否签到
           $yes = $model->where("user_id",$user_id)
               ->whereTime("last_sign_time","yesterday")
               ->order("id desc")
               ->find();
           if($yes){
                //昨天签到了,获取连续签到奖励
               $data[] = ["last_sign_time"=>time(),"rewards"=>$lxRewards,"type"=>2,"user_id"=>$user_id];
               //签到天数+1
               $days = $this->request->userInfo['days']+1;
               $todayRewards = bcadd($todayRewards,$lxRewards,2);//累计签到奖励
           }else{
               //签到天数从1开始
               $days = 1;
           }
           $balance = $this->request->userInfo['balance'];
           //累计用户余额
           $userBalance = bcadd($balance,$todayRewards,2 );
           try{
               Db::startTrans();
               //记录签到日志
               $model->saveAll($data);
               //计算签到总奖励
               $totalRewards = round($model->where("user_id",$user_id)->sum("rewards"),2);
                //保存用户信息
               $this->request->userInfo->save(["days"=>$days,"total_rewards"=>$totalRewards,"balance"=>$userBalance]);
               $balance = bcadd($balance,$dayRewards,2);//累计签到奖励余额
               $username =  $this->request->userInfo->nickname;
               //记录账单
               capital_flow($user_id,0,9,1,$dayRewards,$balance,"{user.signrewards} {capital.money}{$dayRewards}","用户{$username}签到奖励\${$dayRewards}");
               if($yes){
                   $balance = bcadd($balance,$lxRewards,2);
                   capital_flow($user_id,0,9,1,$lxRewards,$balance,"{user.lxsignrewards} {capital.money}$lxRewards}","用户{$username}连续签到奖励\${$lxRewards}");
               }
               $balance = bcadd($balance,$platRewards,2);
               capital_flow($user_id,0,9,1,$platRewards,$balance,"{user.platformrewards} {capital.money}{$platRewards}","用户{$username}平台签到奖励\${$platRewards}");
               Db::commit();
           }catch (\Exception $e){
               Db::rollBack();
               $this->error($e->getMessage());
           }
       }else{
           //查询一个小时内是否有签到记录
           $hourSign = \app\api\model\UserSign::where("user_id",$user_id)
               ->where("last_sign_time",">",time()-3600)
               ->find();
           if($hourSign)
           {
               $this->error("Failed to sign in");
           }
           $balance = $this->request->userInfo['balance'];
           $balance = bcadd($balance,$platRewards,2);
           $model->save(['last_sign_time'=>time(),"rewards"=>$platRewards,"type"=>3,"user_id"=>$user_id]);
           $totalRewards = round($model->where("user_id",$user_id)->sum("rewards"),2);
           $this->request->userInfo->save(["total_rewards"=>$totalRewards,"balance"=>$balance]);
           capital_flow($user_id,0,9,1,$platRewards,$balance,"{user.platformrewards} {capital.money}{$platRewards}","用户{$this->request->userInfo->nickname}平台签到奖励{capital.money}{$platRewards}");
       }
       $this->success("Sign in successfully");
    }
}