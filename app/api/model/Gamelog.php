<?php
declare (strict_types = 1);

namespace app\api\model;

use think\Model;

/**
 * @mixin \think\Model
 */
class Gamelog extends Model
{
    //
	protected $name = 'game_log';
	
	public function gamelist()
	{
		return $this->hasOne(GameList::class,'id','gid');
	}
}
