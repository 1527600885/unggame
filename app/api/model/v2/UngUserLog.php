<?php


namespace app\api\model\v2;


use think\Model;

class UngUserLog extends Model
{
    protected $type = [
        "add_time"=>"timestamp"
    ];
    public function searchTypeAttr($query,$value){
        if($value)
        {
            $query->where("type",$value);
        }
    }
}