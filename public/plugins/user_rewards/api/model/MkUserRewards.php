<?php
// +----------------------------------------------------------------------
// | OneKeyAdmin [ Believe that you can do better ]
// +----------------------------------------------------------------------
// | Copyright (c) 2020-2023 http://onekeyadmin.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: MUKE <513038996@qq.com>
// +----------------------------------------------------------------------
namespace plugins\user_rewards\api\model;

use app\api\model\User;
use think\Model;

class MkUserRewards extends Model
{
    protected $name = "user_rewards";
    public function user(){
        return $this->belongsTo(User::class,"user_id");
    }
}