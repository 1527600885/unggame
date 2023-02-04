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

class Statistics extends Model
{
    protected $name = 'app_statistics_day_ip';

    // 搜索器
    public function searchKeywordAttr($query, $value, $array)
    {
    	if (! empty($value)) {
	        $query->where("ip|country|province|city|url|keyword|keyword_from",'like', '%' . $value . '%');
	    }
    }

    public function searchDateAttr($query, $value, $array)
    {
    	if (! empty($value)) { 
    		$query->whereBetweenTime('create_time', $value[0], $value[1]);
	    }
    }
}