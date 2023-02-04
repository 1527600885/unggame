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
namespace plugins\statistics\index\controller;

use app\index\BaseController;
use app\index\model\Config;
use plugins\statistics\admin\model\CountDay;
use plugins\statistics\admin\model\Statistics as StatisticsModel;

class Index extends BaseController
{
    /**
     * 在线时长
     */
    public function index()
    {
        if ($this->request->isPost()) {
            $config = Config::getVal('app_statistics');
            if (isset($config['open']) && $config['open'] === 1) {
                $duration = 10;
                $user_id  = empty(request()->userInfo) ? 0 : request()->userInfo->id;
                $where[]  = ['ip', '=', request()->ip()];
                $where[]  = ['user_id', '=', $user_id];
                $statistics = StatisticsModel::where($where)->order('create_time','desc')->find();
                if ($statistics) {
                    // 时长统计、在线
                    $statistics->update_time = date('Y-m-d H:i:s');
                    $statistics->duration += $duration;
                    $statistics->save();
                    // 日统计
                    $count = CountDay::whereDay('day')->find();
                    $count->duration += $duration;
                    $count->save();
                    return json(['status'=>'suceess', 'message'=>'完成统计']);
                }
            }
        }
    }
}