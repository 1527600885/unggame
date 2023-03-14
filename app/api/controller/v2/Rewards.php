<?php


namespace app\api\controller\v2;


use app\api\BaseController;
use app\api\model\UserRewards;
use think\Validate;

class Rewards extends BaseController
{
    public function subrewards()
    {
        $user_id = $this->request->userInfo['id'];
        $param = $this->request->param();
        $validate = new Validate([
            "image"=>"require",
            "rewards_id"=>"require|number|>0"
        ]);
        if(!$validate->check($param)){
            $this->error($validate->getError());
        }

        $times = \app\api\model\Rewards::where("id",$param['rewards_id'])->where("status",1)->value("times");
        if(!$times) $this->error("You are unable to obtain this type of reward.");
        $user_times = UserRewards::where("user_id",$user_id)->where("rewards_id",$param['rewards_id'])->count();
        if($user_times >= $times) $this->error("You have reached the limit for the number of times you can receive this reward.");
        $data = [
            "user_id"=>$user_id,
            "rewards_id"=>$param['rewards_id'],
            "image"=>$param['image'],
            "create_time"=>time(),
            "status"=>0
        ];
        UserRewards::create($data);
        $this->success("Submission successful.");
    }

}