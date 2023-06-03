<?php


namespace app\api\controller\v2;


use app\api\BaseController;
use app\api\validate\TeamApply;

class Team extends BaseController
{
    public function joinTeam()
    {
        $param = input("param.");
        $validate = new TeamApply();
        if(!$validate->check($param))
        {
            $this->error($validate->getError());
        }
        $param['user_id'] = $this->request->userInfo->id;
        $apply = \app\api\model\v2\TeamApply::where(["user_id"=>$this->request->userInfo->id,"type"=>$param['type']])->find();
        if($apply){
            if($apply->status == 0 || $apply->status == 1){
                $this->error("You have already submitted it ");
            }
            $apply->save($param);
        }else{
            \app\api\model\v2\TeamApply::create($param);
        }
        $this->success("Submitted successfully");
    }
}