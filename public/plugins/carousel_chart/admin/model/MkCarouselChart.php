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
namespace plugins\carousel_chart\admin\model;

use think\Model;

class MkCarouselChart extends Model
{
    protected $name = "carousel_chart";
    
    // 搜索器
    public function searchKeywordAttr($query, $value)
    {
    	if (! empty($value)) {
	        $query->where("image|url","like", "%" . $value . "%");
	    }
    }
    
    public function searchDateAttr($query, $value, $array)
    {
        if (! empty($value)) { 
            $query->whereBetweenTime("", $value[0], $value[1]);
        }
    }
    public function searchStatusAttr($query,$value,$array){
        if(isset($value) && $value != '')
        {
            $query->where("status",$value);
        }
    }
}