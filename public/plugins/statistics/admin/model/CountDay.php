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
namespace plugins\statistics\admin\model;

use think\Model;

class CountDay extends Model
{
    protected $name = 'app_statistics_day_count';
    
    public function searchDateAttr($query, $value, $array)
    {
    	if (! empty($value)) { 
    		$query->whereBetweenTime('day', $value[0], $value[1]);
	    }
    }
}