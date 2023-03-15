<?php


namespace app\api\controller\v2;


use app\api\BaseController;
use think\Validate;

class GameInteraction extends BaseController
{
    public function submit()
    {
        $data = input("param.");
        $validate = new Validate(["game_id"=>"require","type"=>"require|in:1,2"]);
        if(!$validate->check($data)){
           $this->error( $validate->getError());
        }
        $inter = \app\api\model\v2\GameInteraction::where(["game_id"=>$data['game_id'],"user_id"=>$this->request->userInfo['id']])->find();
        $type = [1=>"is_favorite",2=>"is_liked"];
        if(!$inter)
        {
            \app\api\model\v2\GameInteraction::create(["game_id"=>$data['game_id'],"user_id"=>$this->request->userInfo['id'],$type[$data['type']]=>1,"created_at"=>time(),"updated_at"=>time()]);
        }else{
            $save = [$type[$data['type']]=>1-$inter[$data['type']],"updated_at"=>time()];
            $inter->save($save);
        }
        $this->success("success");

    }
}