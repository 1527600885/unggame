<?php


namespace app\api\model\v2;


use app\common\lib\Redis;
use think\Model;

class Payment extends Model
{
    public function getNameAttr($value,$data)
    {
        if(stripos($value,"(")!==false)
        {
            $value = explode("(",$value)[0];
        }
        return $value;
    }

}