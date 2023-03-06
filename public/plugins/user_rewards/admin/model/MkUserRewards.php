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
namespace plugins\user_rewards\admin\model;

use app\admin\model\User;
use app\api\model\Rewards;
use think\Model;

class MkUserRewards extends Model
{
    protected $name = "user_rewards";
    // 搜索器
    public function searchKeywordAttr($query, $value)
    {
    	if (! empty($value)) {
	        $query->where("image","like", "%" . $value . "%");
	    }
    }
    
    public function searchDateAttr($query, $value, $array)
    {
        if (! empty($value) && !empty($value[0]) && !empty($value[1])) {
            $query->whereBetweenTime("create_time", strtotime($value[0]), strtotime($value[1]));
        }
    }
    public function user()
    {
        return $this->belongsTo(User::class,"user_id")->bind(["user_nickname"=>"nickname"]);
    }
    public function rewards()
    {
        return $this->belongsTo(Rewards::class,"rewards_id")->bind(["type"=>"name"]);
    }
    public function getCreateTimeAttr($value){
        return date("Y-m-d H:i:s",$value);
    }
    public function getStatusTextAttr($value,$data)
    {
        $list = [0=>"待审核",1=>"有效","无效"];
        return $list[$data['status']];
    }
    public function getCheckTimeAttr($value)
    {
        if(!$value){
            return "待审核";
        }
        return date("Y-m-d H:i:s",$value);
    }
}