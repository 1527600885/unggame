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
namespace plugins\user\api\model;

use think\Model;

class MkUserIdcard extends Model
{
    protected $name = "user_idcard";
    public function getStatusTextAttr($value,$data)
    {
        $list = [0=>"待审核",1=>"审核通过",2=>"审核失败"];
        return $list[$data['status']];
    }
}