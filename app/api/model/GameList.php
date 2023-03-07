<?php
declare (strict_types = 1);

namespace app\api\model;

use think\Model;

/**
 * @mixin \think\Model
 */
class GameList extends Model
{
    //
	protected $name = 'gamelist';
	public function getImagesAttr($value){
	    return json_decode($value,true);
    }
}
