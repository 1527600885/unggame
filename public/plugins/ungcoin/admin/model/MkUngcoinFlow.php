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
namespace plugins\ungcoin\admin\model;

use think\Model;

class MkUngcoinFlow extends Model
{
    protected $name = "ungcoin_flow";
    
    // 搜索器
    public function searchKeywordAttr($query, $value)
    {
    	if (! empty($value)) {
	        $query->where("","like", "%" . $value . "%");
	    }
    }
    
    public function searchDateAttr($query, $value, $array)
    {
        if (! empty($value)) { 
            $query->whereBetweenTime("", $value[0], $value[1]);
        }
    }
}