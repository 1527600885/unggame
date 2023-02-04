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
namespace plugins\statistics\admin\controller;

use think\facade\View;
use app\admin\BaseController;
use app\admin\model\Config as ConfigModel;
/**
 * 统计设置
 */
class Config extends BaseController
{
    public function index()
    {
        $name = 'app_statistics';
        if ($this->request->isPost()) {
            $msg = ConfigModel::setVal($name, '统计设置', input('post.value'));
            return json($msg);
        } else {
            $config = ConfigModel::getVal($name);
            if (! $config) {
                $config = [
                    'open'                         => 0,
                    'tencent_location_key'        => "",
                ];
            }
            View::assign('config', $config);
            return View::fetch();
        }
    }
}