<?php


namespace app\api\model\v2;


use think\Model;

class GameInteraction extends Model
{
    protected $type = [
        "updated_at"=>"timestamp"
    ];
    public function game()
    {
        return $this->belongsTo(GameList::class,"game_id");
    }

}