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
namespace plugins\test\admin\model;

use think\Model;

class MkTest extends Model
{
    protected $name = "test";
    protected $json = ["images"];
    protected $jsonAssoc = false;
    
    // 搜索器
    public function searchKeywordAttr($query, $value)
    {
    	if (! empty($value)) {
	        $query->where("images","like", "%" . $value . "%");
	    }
    }
    
    public function searchDateAttr($query, $value, $array)
    {
        if (! empty($value)) { 
            $query->whereBetweenTime("", $value[0], $value[1]);
        }
    }

}