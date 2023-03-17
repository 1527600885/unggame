<?php


namespace app\api\model\v2;


use think\Model;

class GameInteraction extends Model
{
    protected $type = [
        "update_at"=>"timestamp"
    ];
    public function game()
    {
        return $this->belongsTo(GameList::class,"game_id");
    }
    public function getUpdateAtAttr($value)
    {
        return date("Y-m-d H:i:s",$value);
    }
}