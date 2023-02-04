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
use plugins\statistics\admin\model\Statistics;
use plugins\statistics\admin\model\CountDay;
/**
 * 流量分析
 */
class Flow extends BaseController
{
    /**
     * 实时访客
     */
    public function visitors()
    {
        if ($this->request->isPost()) {
            $input = input();
            $order = $input['order'] === 'ascending' ? 'asc' : 'desc';
            $headerCount = Statistics::withSearch(['keyword','date'], $input)->count();
            $data  = Statistics::withSearch(['keyword','date'], $input)
            ->order($input['prop'], $order)
            ->page($input['page'], $input['pageSize'])
            ->select();
            // 记录
            foreach ($data as $key => $val) {
                $data[$key]['duration']     = secto_time($val['duration']);
                $data[$key]['entryUrl']     = empty($val['referrer']) ? '直接访问' : $val['url'];
                $data[$key]['referrer']     = empty($val['referrer']) ? '直接访问' : $val['referrer'];
                $data[$key]['keyword_from'] = empty($val['keyword_from']) ? '直接访问' : $val['keyword_from'];
                $data[$key]['keyword']      = empty($val['keyword']) ? '--' : $val['keyword'];
                $data[$key]['newuser']      = $val['newuser'] === 1 ? '新用户' : '老用户';
            }
            return json(['status' => 'success', 'message' => '获取成功', 'data' => $data, 'count' => $headerCount]);
        } else {
            return View::fetch();
        }
    }
    /**
     * 实时访客图表
     */
    public function visitorsChart()
    {
        $input = input();
        $cate = [];
        $data = [];
        $data[0]['name'] = "浏览量(PV)";
        $data[0]['data'] = [];
        $data[1]['name'] = "访客数(UV)";
        $data[1]['data'] = [];
        $time = time();
        for ($i=-1; $i < 30; $i++) {
            $startTime = $time - ($i+1) * 60;
            $endTime   = $time - $i *60;
            array_push($cate, date('H:i', $startTime) . ' - ' . date('H:i', $endTime));
            $start = date('Y-m-d H:i', $startTime);
            $end   = date('Y-m-d H:i', $endTime);
            $pv = Statistics::whereTime('update_time', 'between', [$start, $end])->count();
            $uv = Statistics::whereTime('update_time', 'between', [$start, $end])->group('ip,user_id')->count();
            array_push($data[0]['data'], $pv);
            array_push($data[1]['data'], $uv);
        }
        $online = $data[1]['data'][1];
        $date   = date('Y/m/d H:i:s');
        return json(['status' => 'success', 'message' => '获取成功', 'data' => $data , 'categories' => $cate, 'online' => $online, 'date' => $date]);
    }

    /**
     * 趋势分析
     */
    public function trend()
    {
        if ($this->request->isPost()) {
            $input = input();
            $data  = CountDay::withSearch(['date'], $input)->select()->toArray();
            foreach ($data as $key => $val) {
                $data[$key]['duration_avg'] = '--';
                $data[$key]['quit'] = '--';
                if ($data[$key]['ip'] !== 0 && $key !== 3) {
                    // 平均访问时长  
                    $data[$key]['duration_avg'] = secto_time(ceil($data[$key]['duration'] / $data[$key]['ip']));
                    // 跳出率
                    $data[$key]['quit'] = round(($data[$key]['ip'] / $data[$key]['pv']) * 100, 2) . '%';
                }
            }
            $headerCount['ip']           = CountDay::withSearch(['date'], $input)->sum('ip');
            $headerCount['pv']           = CountDay::withSearch(['date'], $input)->sum('pv');
            $headerCount['uv']           = CountDay::withSearch(['date'], $input)->sum('uv');
            $headerCount['duration']     = CountDay::withSearch(['date'], $input)->sum('duration'); 
            $headerCount['duration_avg'] = $headerCount['ip'] == 0 ? '--' : secto_time(ceil($headerCount['duration'] / $headerCount['ip']));
            $headerCount['quit']         = $headerCount['ip'] == 0 ? '--' : round(($headerCount['ip'] / $headerCount['pv']) * 100, 2) . '%';
            // 图形
            $categories = [];
            $chartData[0]['name'] = '浏览量(PV)';
            $chartData[0]['data'] = [];
            $chartData[1]['name'] = '访客数(UV)';
            $chartData[1]['data'] = [];
            $chartData[2]['name'] = 'IP数';
            $chartData[2]['data'] = [];
            $interval = (strtotime($input['date'][1]) - strtotime($input['date'][0])) / (3600 * 24);
            $dateArr  = array_column($data, 'day');
            for ($i = 0; $i < $interval; $i++) { 
                $time = strtotime($input['date'][0]) + (3600 * 24 * $i);
                $date = date('Y-m-d', $time);
                array_push($categories, $date);
                $index = array_search($date, $dateArr);
                $pv = $index === false ? 0 : $data[$index]['pv'];
                $uv = $index === false ? 0 : $data[$index]['uv'];
                $ip = $index === false ? 0 : $data[$index]['ip'];
                array_push($chartData[0]['data'], $pv);
                array_push($chartData[1]['data'], $uv);
                array_push($chartData[2]['data'], $ip);
            }
            return json([
                'status'     => 'success', 
                'message'    => '获取成功', 
                'data'       => $data, 
                'headerCount'=> $headerCount, 
                'categories' => $categories,
                'chartData'  => $chartData,
            ]);
        } else {
            return View::fetch();
        }
    }
}