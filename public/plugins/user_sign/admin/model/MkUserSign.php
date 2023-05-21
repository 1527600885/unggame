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
namespace plugins\user_sign\admin\model;

use think\Model;
use app\admin\model\User;
class MkUserSign extends Model
{
    protected $name = "user_sign";
    protected $type = [
        "last_sign_time"=>"timestamp"
    ];
    // 搜索器
    public function searchKeywordAttr($query, $value)
    {
    	if (! empty($value)) {
	        $query->where("","like", "%" . $value . "%");
	    }
    }

    public function searchDateAttr($query, $value, $array)
    {
        if (! empty($value)) {
            $query->where("last_sign_time",">",$value[0])->where("last_sign_time","<",$value[1]);
        }
    }
    public function getTypeTextAttr($value,$data)
    {
        $list = [1=>"普通签到奖励",2=>"普通签到奖励",3=>"平台奖励"];
        return $list[$data['type']];
    }
    public function user()
    {
        return $this->belongsTo(User::class,"user_id")->bind(["nickname"=>"nickname","total_days"=>"total_days"]);
    }
}