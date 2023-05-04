<?php

namespace app\api\model\v2;

use app\common\lib\Redis;
use Hashids\Hashids;
use think\Model;

class GameList extends Model
{
    protected $name = 'gamelist';
    public function getGameNameAttr($value,$data)
    {
        $gameName=json_decode($data['gameName'],true);
        global $lang;
        return croppstring($gameName[$lang],12);
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
    public function getImagesAttr($value){
        return json_decode($value,true);
    }
    public function getAuthorAttr($value)
    {
        return explode(",",$value);
    }
    public function getNicknameAttr($value,$data)
    {
        $redis = (new Redis())->getRedis();
        $key = "bigwin_game_nickname_{$data['id']}";
        $nickname = $redis->get($key);
        if(!$nickname){
            $hashids = new Hashids(env('hashids'), 6,env('hashids_write'));
            $nickname = $hashids->encode($data['game_id']);
            $redis->set($key,$nickname,600);
        }
        return $nickname;
    }
    public function getPriceAttr($value,$data)
    {
        $redis = (new Redis())->getRedis();
        $key = "bigwin_game_price_{$data['id']}";
        $price = $redis->get($key);
        if(!$price){
            $price = mt_rand(1000,1000000);
            $price = bcmul($price,0.01,2);
            $redis->set($key,$price,600);
        }
        return $price;
    }
}