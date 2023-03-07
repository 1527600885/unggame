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
        
    }
}