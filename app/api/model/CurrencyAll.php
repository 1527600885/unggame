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
	public static function getDataByName($name)
    {
        $currency = CurrencyAll::where("is_show", 1)->cache("currency_all_show", 600)->field("id,name,type,country,symbol,thumb_img,url_list,payment_ids,withdrawl_ids")->select()->toArray();
        $lists = array_column($currency, NULL, "name");
        return $lists[$name];
    }
}
