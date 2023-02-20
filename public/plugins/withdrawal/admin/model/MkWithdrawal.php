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
namespace plugins\withdrawal\admin\model;

use think\Model;

class MkWithdrawal extends Model
{
    protected $name = "withdrawal";
	// 设置json类型字段
	protected $json = ['field'];
	protected $jsonAssoc = true;
	protected $dateFormat=["pay_time","status_time"];
    
	// 关联模型
	public function group()
	{
	    return $this->hasOne(User::class, 'id', 'uid')->bind([
	        'nickname' => 'nickname'
	    ]);
	}
    // 搜索器
    public function searchKeywordAttr($query, $value)
    {
    	if (! empty($value)) {
	        $query->where("currency|name|address|other|amount|money|charge","like", "%" . $value . "%");
	    }
    }
    
    public function searchDateAttr($query, $value, $array)
    {
        if (! empty($value)) { 
            $query->whereBetweenTime("", $value[0], $value[1]);
        }
    }
    public function getOnlineStatusNameAttr($value,$data)
    {
        $list = [0=>"未转账",1=>"转账中",2=>"<span style='color:green;'>转账成功</span>",3=>"<span style='color:red;'>转账失败</span>"];
        return $list[$data['online_status']];
    }
    public function getAddTimesAttr($value,$data){
	    return date("Y-m-d",$data['add_time']);
    }
    public function getTypeNameAttr($value,$data)
    {
        $list = [1=>"数字货币提现",2=>"在线提现"];
        return $list[$data['type']];
    }
    public function getStatusTimeAttr($value,$data)
    {

       return $value ? date("Y-m-d H:i:s",$data['status_time']):$value;
    }
    public function getPayTimeAttr($value,$data)
    {
        return $value  ? date("Y-m-d H:i:s",$data['pay_time']) : $value;
    }
}