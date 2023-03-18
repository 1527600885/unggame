<?php


namespace app\api\model\v2;


use app\common\lib\Redis;
use Hashids\Hashids;
use think\Model;

class TopGame extends Model
{
    public function getNicknameAttr($value,$data)
    {
        $redis = (new Redis())->getRedis();
        $key = "top_game_nickname_{$data['type']}_{$data['game_id']}";
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
        $key = "top_game_price_{$data['type']}_{$data['game_id']}";
        $price = $redis->get($key);
        if(!$price){
            if($data['type'] == 1)
            {
                $price = mt_rand(50000,5000000);
            }else{
                $price = mt_rand(10000,1000000);
            }
            $price = bcmul($price,0.01,2);
            $redis->set($key,$price,600);
        }
        return $price;
    }
}