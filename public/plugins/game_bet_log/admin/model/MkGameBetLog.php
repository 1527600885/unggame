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
namespace plugins\game_bet_log\admin\model;

use think\Model;
use app\admin\model\GameList;
use app\admin\model\User;
class MkGameBetLog extends Model
{
    protected $connection = 'mysql2';
    protected $name = "game_bet_log";
    protected $type = [
        "betTime"=>"timestamp",
        "endTime"=>"timestamp"
    ];
    
    // 搜索器
    public function searchKeywordAttr($query, $value)
    {
    	if (! empty($value)) {
	        $query->where("tcgGameCode|betAmount|game_account|netPnl|betOrderNo|rake|merchantCode","like", "%" . $value . "%");
	    }
    }
    
    public function searchDateAttr($query, $value, $array)
    {
        if (! empty($value)) {
            $query->where("betTime",">",$value[0])->where("betTime","<",$value[1]);
        }
    }
    public function getGameNameAttr($value)
    {
        if($value){
            $value = json_decode($value,true)["EN"];
            return $value;
        }
    }
    public function game()
    {
        return $this->belongsTo(GameList::class,"game_id")->bind(["gameName"=>"gameName"]);
    }
    public function user()
    {
        return $this->belongsTo(User::class,"user_id")->bind(["nickname"=>"nickname"]);
    }
}