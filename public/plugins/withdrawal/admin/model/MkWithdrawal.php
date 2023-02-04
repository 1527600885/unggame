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
}