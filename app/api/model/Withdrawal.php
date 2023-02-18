<?php
declare (strict_types = 1);

namespace app\api\model;

use think\Model;

/**
 * @mixin \think\Model
 */
class Withdrawal extends Model
{
    //
	protected $name = 'Withdrawal';
	public function searchTypeAttr($query, $value, $array)
    {
        if(isset($value) && $value>=0){
            $query->where("online_status",$value);
        }
    }
    public function getTypeTextAttr($value,$data)
    {
        if(isset($data['type'])&& !empty($data['type'])){
            $typeList = [1=>"Cryptocurrency",2=>"Cash currency"];
            $value = $typeList[$data['type']];
        }
        return $value;
    }
}
