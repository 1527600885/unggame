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
namespace plugins\app\api\model;

use think\Model;

class Versions extends Model
{
    protected $name = "app_versions";
//    protected $type = [
//        "release_date"=>"timestamp"
//    ];
//    public function setReleaseDateAttr($value,$data)
//    {
//        echo 123;
//       return date("Y-m-d", strtotime($value));
//    }
}