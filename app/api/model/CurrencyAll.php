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
	protected $type = ['url_list'=>'json',"awards"=>"json"];
}
