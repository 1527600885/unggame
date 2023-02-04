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
namespace plugins\statistics\index\listen;

use app\index\model\Config;
use plugins\statistics\addons\Request;
use plugins\statistics\index\model\CountDay;
use plugins\statistics\index\model\Statistics;
use plugins\statistics\addons\TencentLocationService;

class HtmlCheck
{
    public function handle($response)
    {
        if (! empty(request()->catalog['id'])) {
            $config = Config::getVal('app_statistics');
            if (isset($config['open']) && $config['open'] === 1) {
                // 在线统计
                $bind = "\n<script>setInterval(function(){ post('statistics/index/index', {}, function(){}); }, 10000);</script>\n";
                $content = str_replace('</head>', '</head>'.$bind, $response->getContent());
                $response->content($content);
                // 访问统计
                $tencent  = new TencentLocationService();
                $location = session('location');
                $location = empty($location) ? $tencent->ipLocation() : unserialize($location);
                session('location', serialize($location));
                $keyword  = Request::keyword();
                $user_id  = empty(request()->userInfo) ? 0 : request()->userInfo->id;
                $duration = 2;
                if (empty($user_id)) {
                    $newuser = Statistics::where('ip', request()->ip())->value('id') ? 0 : 1;
                } else {
                    $newuser = Statistics::where('ip', request()->ip())->whereOr('user_id', $user_id)->value('id') ? 0 : 1;
                }
                // 统计每日
                $count = CountDay::whereDay('day')->find();
                $dayIp = Statistics::whereDay('create_time')->where('ip', request()->ip())->value('id');
                $dayUv = Statistics::whereDay('create_time')->where('ip', request()->ip())->where('user_id', $user_id)->value('id');
                if ($count) {
                    $count->pv = $count->pv + 1;
                    $count->duration += $duration;
                    if (! $dayIp) $count->ip = $count->ip + 1;
                    if (! $dayUv) $count->uv = $count->uv + 1;
                    $count->save();
                } else {
                    CountDay::create([
                        'day'      => date('Y-m-d'),
                        'ip'       => 1,
                        'pv'       => 1,
                        'uv'       => 1,
                        'duration' => $duration,
                    ]);
                }
                $date = date('Y-m-d H:i:s');
                // 统计每次
                Statistics::create([
                    'ip'           => request()->ip(),
                    'user_id'      => $user_id,
                    'mobile'       => request()->isMobile() ? 1 : 0,
                    'newuser'      => $newuser,
                    'url'          => request()->url(true),
                    'title'        => request()->catalog['seo_title'],
                    'os'           => Request::os(),
                    'broswer'      => Request::browser(),
                    'referrer'     => Request::referer(),
                    'keyword'      => $keyword['name'],
                    'keyword_from' => $keyword['from'],
                    'duration'     => $duration,
                    'country'      => $location['nation'],
                    'province'     => $location['province'],
                    'city'         => $location['city'],
                    'update_time'  => $date,
                    'create_time'  => $date,
                ]);
            }
        }
    }
}