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
namespace plugins\ung\admin\model;

use app\admin\model\User;
use think\Model;

class MkUngUser extends Model
{
    protected $name = "ung_user";
    protected $type = [
      "update_time" => 'timestamp',
       "add_time"=> 'timestamp'
    ];
    // 搜索器
    public function searchKeywordAttr($query, $value)
    {
    	if (! empty($value)) {
	        $query->where("num","like", "%" . $value . "%");
	    }
    }
    
    public function searchDateAttr($query, $value, $array)
    {
        if (! empty($value)) { 
            $query->whereBetweenTime("", $value[0], $value[1]);
        }
    }
    public function user()
    {
        return $this->belongsTo(User::class,"uid")->bind(["nickname"=>"nickname"]);
    }
}