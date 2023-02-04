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
use plugins\statistics\admin\model\CountDay;
use plugins\statistics\admin\model\Statistics;
/**
 * 访问分析
 */
class Visit extends BaseController
{
    /**
     * 受访页面
     */
    public function url()
    {
        if ($this->request->isPost()) {
            $input = input();
            $where[] = ['url', '<>', '']; 
            $count = Statistics::withSearch(['date'], $input)->where($where)->group('url')->count();
            $data  = Statistics::withSearch(['date'], $input)
            ->where($where)
            ->field('url, count(id) as pv')
            ->group('url')
            ->order('pv', 'desc')
            ->page($input['page'], $input['pageSize'])
            ->select()
            ->toArray();
            foreach ($data as $key => $val) {
                $subWhere[0] = ['url', '=', $val['url']];
                $data[$key]['ip'] = Statistics::withSearch(['date'], $input)->where($subWhere)->group('ip')->count();
                $data[$key]['uv'] = Statistics::withSearch(['date'], $input)->where($subWhere)->group('ip,user_id')->count();
                $data[$key]['duration'] = Statistics::withSearch(['date'], $input)->where($subWhere)->sum('duration');
                $data[$key]['duration_avg'] = $data[$key]['ip'] == 0 ? '--' : secto_time(ceil($data[$key]['duration'] / $data[$key]['ip']));
                $data[$key]['quit'] = $data[$key]['ip'] == 0 ? '--' : round(($data[$key]['ip'] / $data[$key]['pv']) * 100, 2) . '%';
            }
            return json([
                'status'     => 'success', 
                'message'    => '获取成功', 
                'count'      => $count,
                'data'       => $data, 
            ]);
        } else {
            return View::fetch();
        }
    }

    /**
     * 受访页面图表
     */
    public function urlChart()
    {
        if ($this->request->isPost()) {
            $input = input();
            $headerCount['ip']           = CountDay::withSearch(['date'], $input)->sum('ip');
            $headerCount['pv']           = CountDay::withSearch(['date'], $input)->sum('pv');
            $headerCount['uv']           = CountDay::withSearch(['date'], $input)->sum('uv');
            $headerCount['duration']     = CountDay::withSearch(['date'], $input)->sum('duration'); 
            $headerCount['duration_avg'] = $headerCount['ip'] == 0 ? '--' : secto_time(ceil($headerCount['duration'] / $headerCount['ip']));
            $headerCount['quit']         = $headerCount['ip'] == 0 ? '--' : round(($headerCount['ip'] / $headerCount['pv']) * 100, 2) . '%';
            return json([
                'status'     => 'success', 
                'message'    => '获取成功', 
                'headerCount'=> $headerCount
            ]);
        }
    }

    /**
     * 入口页面
     */
    public function entryUrl()
    {
        if ($this->request->isPost()) {
            $input = input();
            $where[] = ['referrer', '<>', '']; 
            $where[] = ['url', '<>', '']; 
            $count = Statistics::withSearch(['date'], $input)->where($where)->group('url')->count();
            $data  = Statistics::withSearch(['date'], $input)
            ->where($where)
            ->field('url, count(id) as pv')
            ->group('url')
            ->order('pv', 'desc')
            ->page($input['page'], $input['pageSize'])
            ->select()
            ->toArray();
            foreach ($data as $key => $val) {
                $subWhere[0] = ['url', '=', $val['url']];
                $subWhere[1] = ['referrer', '<>', ''];
                $data[$key]['ip'] = Statistics::withSearch(['date'], $input)->where($subWhere)->group('ip')->count();
                $data[$key]['uv'] = Statistics::withSearch(['date'], $input)->where($subWhere)->group('ip,user_id')->count();
                $data[$key]['duration'] = Statistics::withSearch(['date'], $input)->where($subWhere)->sum('duration');
                $data[$key]['duration_avg'] = $data[$key]['ip'] == 0 ? '--' : secto_time(ceil($data[$key]['duration'] / $data[$key]['ip']));
                $data[$key]['quit'] = $data[$key]['ip'] == 0 ? '--' : round(($data[$key]['ip'] / $data[$key]['pv']) * 100, 2) . '%';
            }
            return json([
                'status'     => 'success', 
                'message'    => '获取成功', 
                'count'      => $count,
                'data'       => $data, 
            ]);
        } else {
            return View::fetch();
        }
    }

    /**
     * 入口页面
     */
    public function entryUrlChart()
    {
        if ($this->request->isPost()) {
            $input = input();
            $headerCount['ip']           = Statistics::withSearch(['date'], $input)->where('referrer','<>','')->group('ip')->count();
            $headerCount['pv']           = Statistics::withSearch(['date'], $input)->where('referrer','<>','')->count();
            $headerCount['uv']           = Statistics::withSearch(['date'], $input)->where('referrer','<>','')->group('ip,user_id')->count();
            $headerCount['duration']     = Statistics::withSearch(['date'], $input)->where('referrer','<>','')->sum('duration');
            $headerCount['duration_avg'] = $headerCount['ip'] == 0 ? '--' : secto_time(ceil($headerCount['duration'] / $headerCount['ip']));
            $headerCount['quit']         = $headerCount['ip'] == 0 ? '--' : round(($headerCount['ip'] / $headerCount['pv']) * 100, 2) . '%';
            $pieData = [];
            $where[] = ['referrer', '<>', '']; 
            $data  = Statistics::withSearch(['date'], $input)
            ->where($where)
            ->field('url, count(id) as pv')
            ->group('url')
            ->order('pv', 'desc')
            ->select()
            ->toArray();
            $categories = [];
            $chartData  = [];
            foreach ($data as $key => $val) {
                $subWhere[0] = ['url', '=', $val['url']];
                $subWhere[1] = ['referrer', '<>', ''];
                // 图形
                array_push($pieData, [$val['url'], $val['pv']]);
                $chartData[$key]['name'] = $val['url'];
                $chartData[$key]['data'] = [];
                $interval = (strtotime($input['date'][1]) - strtotime($input['date'][0])) / (3600 * 24);
                $dateArr  = array_column($data, 'day');
                for ($i = 0; $i < $interval; $i++) { 
                    $time = strtotime($input['date'][0]) + (3600 * 24 * $i);
                    $date = date('Y-m-d', $time);
                    array_push($categories, $date);
                    $dayPv= Statistics::whereDay('create_time', $date)->where($subWhere)->count();
                    array_push($chartData[$key]['data'], $dayPv);
                }
            }
            return json([
                'status'     => 'success', 
                'message'    => '获取成功', 
                'headerCount'=> $headerCount, 
                'pieData'    => $pieData,
                'categories' => $categories,
                'chartData'  => $chartData,
            ]);
        }
    }
}