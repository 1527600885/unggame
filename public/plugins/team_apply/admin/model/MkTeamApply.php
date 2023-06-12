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
namespace plugins\team_apply\admin\model;

use think\Model;

class MkTeamApply extends Model
{
    protected $name = "team_apply";
    protected $append = ["type_text"];
    protected $type = ["create_time"=>"timestamp"];
    
    // 搜索器
    public function searchKeywordAttr($query, $value)
    {
    	if (! empty($value)) {
	        $query->where("mobile|code|name|email|failed_reason|surname|telegram|twitter|whatsapp|message","like", "%" . $value . "%");
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
        $list = [1=>"代理",2=>"代言人"];
        return $list[$data['type']];
    }
}