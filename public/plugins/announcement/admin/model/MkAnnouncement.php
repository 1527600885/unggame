<?php
// +----------------------------------------------------------------------
// | OneKeyAdmin [ Believe that you can do better ]
// +----------------------------------------------------------------------
// | Copyright (c) 2020-2023 http://onekeyadmin.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: MUKE <513038996@qq.com>
// +----------------------------------------------------------------------
namespace plugins\announcement\admin\model;

use app\admin\model\User;
use think\Model;

class MkAnnouncement extends Model
{
    protected $name = "announcement";
    protected $createTime = "create_time";
    protected $autoWriteTimestamp = true ;
    // 搜索器
    public function searchKeywordAttr($query, $value)
    {
    	if (! empty($value)) {
	        $query->where("thumb_image|content|desc","like", "%" . $value . "%");
	    }
    }
    
    public function searchDateAttr($query, $value, $array)
    {
        if (! empty($value)) { 
            $query->whereBetweenTime("", $value[0], $value[1]);
        }
    }
    public function getTypeTextAttr($value,$data)
    {
        $list = [1=>"系统通知",2=>"游戏通知"];
        return $list[$data['type']] ?? $value;
    }
    public function getStatusTextAttr($value,$data)
    {
        $list = [0=>"隐藏",1=>"显示"];
        return $list[$data['status']];
    }
    public static function onBeforeInsert($model)
    {
        $model->create_time = time();
    }
    public function user()
    {
        return $this->belongsTo(User::class,"user_id")->bind(["user_name"=>"nickname"]);
    }
    public function getUserNameAttr($value)
    {
        if(!$value){
            $value = "所有人";
        }
        return $value;
    }
}