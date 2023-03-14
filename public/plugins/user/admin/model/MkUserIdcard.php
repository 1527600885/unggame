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
namespace plugins\user\admin\model;

use think\Model;

class MkUserIdcard extends Model
{
    protected $name = "user_idcard";
    protected $type = [
        "create_time"=>"timestamp",
        "update_time"=>"timestamp",
        "review_time"=>"timestamp"
    ];
    
    // 搜索器
    public function searchKeywordAttr($query, $value)
    {
    	if (! empty($value)) {
	        $query->where("idCard_image|idCard_image_with_hand|surname|name|failed_reason","like", "%" . $value . "%");
	    }
    }
    
    public function searchDateAttr($query, $value, $array)
    {
        if (! empty($value)) { 
            $query->whereBetweenTime("", $value[0], $value[1]);
        }
    }
    public function getStatusTextAttr($value,$data)
    {
        $list = [0=>"待审核",1=>"审核通过",2=>"审核失败"];
        return $list[$data['status']];
    }
}