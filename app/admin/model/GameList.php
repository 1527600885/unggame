<?php
declare (strict_types = 1);

namespace app\admin\model;

use think\Model;

/**
 * @mixin \think\Model
 */
class GameList extends Model
{
    //
	protected $name = 'gamelist';

    public function getGameImageAttr($value){
        return env('aws.imgurl').json_decode($value,true)['EN'];
     }
}
