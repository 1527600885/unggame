<?php


namespace app\api\model\v2;


use think\Model;

class Order extends Model
{
    public function getTypeTextAttr($value,$data)
    {
        if(isset($data['type'])&& !empty($data['type'])){
            $typeList = [1=>"Crypto",2=>"Cash"];
            $value = $typeList[$data['type']];
        }
        return $value;
    }
    public function getStatusTextAttr($value,$data)
    {
        $list = [0=>"Unpaid",1=>"Paid",2=>"Failed"];
        return $list[$data['status']];
    }
}