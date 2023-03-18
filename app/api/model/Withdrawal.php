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
            $typeList = [1=>"Crypto",2=>"Cash"];
            $value = $typeList[$data['type']];
        }
        return $value;
    }
    public function getAddTimeTextAttr($value,$data){
	    return date("Y-m-H:i:s",$data['add_time']);
    }
    public function getPayTimeTextAttr($value,$data)
    {
        if($value){
            $value = date("Y-m-d H:i:s",$value);
        }
        return $value;
    }
}
