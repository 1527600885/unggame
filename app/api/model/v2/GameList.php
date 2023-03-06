<?php


namespace app\api\model\v2;


use think\Model;

class GameList extends Model
{
    protected $name = 'gamelist';
    public function getGameNameAttr($value,$data)
    {
        $gameName=json_decode($data['gameName'],true);
        global $lang;
        return croppstring($gameName[$lang],6);
    }
    public function getGameImageAttr($value,$data){
        $gameImage=json_decode($data['gameImage'],true);
        global  $lang;
        if($lang=='KO'){
            return $gameImage['EN'];
        }elseif($lang=='MS'){
            return $gameImage['ID'];
        }else{
            return $gameImage[$lang];
        }
    }
}