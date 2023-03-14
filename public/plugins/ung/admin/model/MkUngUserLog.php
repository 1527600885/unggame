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

class MkUngUserLog extends Model
{
    protected $name = "ung_user_log";
    protected $type = [
      "add_time"=>"timestamp"
    ];
    public static $start_time = "2023-03-10";
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
            $query->whereBetweenTime("", $value[0], $value[1]);
        }
    }
    public function user()
    {
        return $this->belongsTo(User::class,"uid")->bind(["nickname"=>"nickname"]);
    }
    public function touser()
    {
        return $this->belongsTo(User::class,"touserid")->bind(["tonickname"=>"nickname"]);
    }
    public function getTypeTextAttr($value,$data)
    {
        $list = [1=>"转账",2=>"官方转账",3=>"申购",4=>"赎回"];
        return isset($list[$data["type"]]) ? $list[$data["type"]] : $data["type"];
    }
    public static function getCountData()
    {
        $userNumbers = User::where("last_trade_time","<",7*24*60*60)->count();
        $total = round(self::where("type",4)->sum("total_price"),4);
        $yesterday_divd = round(UngUserDivd::whereTime("create_time","yesterday")->sum("divdmoney"),4);
        $yesterday_price = round(self::where("type",4)->whereTime("add_time","yesterday")->sum("total_price"),4);
        $total_days = intval((time()- strtotime(self::$start_time))/(24*60*60));
        return compact("userNumbers","total","yesterday_divd","yesterday_price","total_days");
    }
}