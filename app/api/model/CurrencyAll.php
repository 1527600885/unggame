<?php
declare (strict_types = 1);

namespace app\api\model;

use think\Model;

/**
 * @mixin \think\Model
 */
class CurrencyAll extends Model
{
    //
	protected $name = 'currency_all';
	public function getUrlListAttr($value)
    {
        if($value){
            return json_decode($value,true);
        }
    }
}
