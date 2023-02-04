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
 * 访客分析
 */
class Visitor extends BaseController
{
    /**
     * 地域分布
     */
    public function area()
    {
        if ($this->request->isPost()) {
            $input = input();
            $name = $input['chartType'];
            $where[] = [$name, '<>', '']; 
            $count = Statistics::withSearch(['date'], $input)->where($where)->group($name)->count();
            $data  = Statistics::withSearch(['date'], $input)
            ->where($where)
            ->field($name.' as name, count(id) as pv')
            ->group($name)
            ->order('pv', 'desc')
            ->page($input['page'], $input['pageSize'])
            ->select()
            ->toArray();
            foreach ($data as $key => $val) {
                $subWhere[0] = [$name, '=', $val['name']];
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
     * 地域分布
     */
    public function areaChart()
    {
        if ($this->request->isPost()) {
            $input = input();
            $name = $input['chartType'];
            $headerCount['ip']           = Statistics::withSearch(['date'], $input)->where($name,'<>','')->group('ip')->count();
            $headerCount['pv']           = Statistics::withSearch(['date'], $input)->where($name,'<>','')->count();
            $headerCount['uv']           = Statistics::withSearch(['date'], $input)->where($name,'<>','')->group('ip,user_id')->count();
            $headerCount['duration']     = Statistics::withSearch(['date'], $input)->where($name,'<>','')->sum('duration');
            $headerCount['duration_avg'] = $headerCount['ip'] == 0 ? '--' : secto_time(ceil($headerCount['duration'] / $headerCount['ip']));
            $headerCount['quit']         = $headerCount['ip'] == 0 ? '--' : round(($headerCount['ip'] / $headerCount['pv']) * 100, 2) . '%';
            $where[] = [$name, '<>', '']; 
            $data  = Statistics::withSearch(['date'], $input)
            ->where($where)
            ->field($name.' as name, count(id) as value')
            ->group($name)
            ->order('value', 'desc')
            ->select()
            ->toArray();
            $count = array_sum(array_column($data, 'value'));;
            foreach ($data as $key => $val) {
                $data[$key]['title'] = $val['name'];
                $data[$key]['percentage'] = round(($val['value'] / $count) * 100, 2);
                $data[$key]['name'] = mb_substr($val['name'], 0, -1);
            }
            return json([
                'status'     => 'success', 
                'message'    => '获取成功', 
                'headerCount'=> $headerCount, 
                'chartData'  => $data,
            ]);
        }
    }

    /**
     * 系统环境
     */
    public function os()
    {
        if ($this->request->isPost()) {
            $input = input();
            $where[] = ['os', '<>', '']; 
            $count = Statistics::withSearch(['date'], $input)->where($where)->group('os')->count();
            $data  = Statistics::withSearch(['date'], $input)
            ->where($where)
            ->field('os, count(id) as pv')
            ->group('os')
            ->order('pv', 'desc')
            ->page($input['page'], $input['pageSize'])
            ->select()
            ->toArray();
            foreach ($data as $key => $val) {
                $subWhere[0] = ['os', '=', $val['os']];
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
     * 系统环境图表
     */
    public function osChart()
    {
        if ($this->request->isPost()) {
            $input = input();
            $headerCount['ip']           = CountDay::withSearch(['date'], $input)->sum('ip');
            $headerCount['pv']           = CountDay::withSearch(['date'], $input)->sum('pv');
            $headerCount['uv']           = CountDay::withSearch(['date'], $input)->sum('uv');
            $headerCount['duration']     = CountDay::withSearch(['date'], $input)->sum('duration'); 
            $headerCount['duration_avg'] = $headerCount['ip'] == 0 ? '--' : secto_time(ceil($headerCount['duration'] / $headerCount['ip']));
            $headerCount['quit']         = $headerCount['ip'] == 0 ? '--' : round(($headerCount['ip'] / $headerCount['pv']) * 100, 2) . '%';
            $pieData = [];
            $data = Statistics::withSearch(['date'], $input)
            ->field('mobile, count(id) as pv')
            ->group('mobile')
            ->select()
            ->toArray();
            foreach ($data as $key => $val) {
                $name = $val['mobile'] == 0 ? '计算机系统' : '系统端系统';
                array_push($pieData, [$name, $val['pv']]);
            }
            return json([
                'status'     => 'success', 
                'message'    => '获取成功', 
                'headerCount'=> $headerCount, 
                'pieData'    => $pieData,
            ]);
        }
    }

    /**
     * 新老访客
     */
    public function newuser()
    {
        if ($this->request->isPost()) {
            $input = input();
            $headerCount['ip']           = CountDay::withSearch(['date'], $input)->sum('ip');
            $headerCount['pv']           = CountDay::withSearch(['date'], $input)->sum('pv');
            $headerCount['uv']           = CountDay::withSearch(['date'], $input)->sum('uv');
            $headerCount['duration']     = CountDay::withSearch(['date'], $input)->sum('duration'); 
            $headerCount['duration_avg'] = $headerCount['ip'] == 0 ? '--' : secto_time(ceil($headerCount['duration'] / $headerCount['ip']));
            $headerCount['quit']         = $headerCount['ip'] == 0 ? '--' : round(($headerCount['ip'] / $headerCount['pv']) * 100, 2) . '%';
            $data = Statistics::withSearch(['date'], $input)
            ->field('newuser, count(id) as pv')
            ->group('newuser')
            ->order('newuser', 'desc')
            ->select()
            ->toArray();
            $count = array_sum(array_column($data, 'pv'));
            foreach ($data as $key => $val) {
                $subWhere[0] = ['newuser', '=', $val['newuser']];
                $data[$key]['ip'] = Statistics::withSearch(['date'], $input)->where($subWhere)->group('ip')->count();
                $data[$key]['uv'] = Statistics::withSearch(['date'], $input)->where($subWhere)->group('ip,user_id')->count();
                $data[$key]['duration'] = Statistics::withSearch(['date'], $input)->where($subWhere)->sum('duration');
                $data[$key]['duration_avg'] = $data[$key]['ip'] == 0 ? '--' : secto_time(ceil($data[$key]['duration'] / $data[$key]['ip']));
                $data[$key]['quit'] = $data[$key]['ip'] == 0 ? '--' : round(($data[$key]['ip'] / $data[$key]['pv']) * 100, 2) . '%';
                $data[$key]['percentage'] = round(($val['pv'] / $count) * 100, 2);
                $data[$key]['referrer'] = Statistics::withSearch(['date'], $input)
                ->where($subWhere)
                ->field('referrer, count(id) as pv')
                ->group('referrer')
                ->limit(5)
                ->order('pv', 'desc')
                ->select()
                ->toArray();
                $data[$key]['entryUrl'] = Statistics::withSearch(['date'], $input)
                ->where($subWhere)
                ->where('referrer', '<>', '')
                ->field('url, count(id) as pv')
                ->group('url')
                ->limit(5)
                ->order('pv', 'desc')
                ->select()
                ->toArray();
            }
            return json([
                'status'      => 'success', 
                'message'     => '获取成功', 
                'data'        => $data, 
                'headerCount' => $headerCount,
            ]);
        } else {
            return View::fetch();
        }
    }

    /**
     * 忠诚度
     */
    public function loyal()
    {
        if ($this->request->isPost()) {
            $input = input();
            $where[] = ['newuser', '<>', '']; 
            $count = Statistics::withSearch(['date'], $input)->where($where)->group('newuser')->count();
            $data  = Statistics::withSearch(['date'], $input)
            ->where($where)
            ->field('newuser, count(id) as pv')
            ->group('newuser')
            ->order('pv', 'desc')
            ->page($input['page'], $input['pageSize'])
            ->select()
            ->toArray();
            foreach ($data as $key => $val) {
                $subWhere[0] = ['newuser', '=', $val['newuser']];
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
     * 忠诚度图表
     */
    public function loyalChart()
    {
        if ($this->request->isPost()) {
            $input = input();
            $headerCount['ip']           = CountDay::withSearch(['date'], $input)->sum('ip');
            $headerCount['pv']           = CountDay::withSearch(['date'], $input)->sum('pv');
            $headerCount['uv']           = CountDay::withSearch(['date'], $input)->sum('uv');
            $headerCount['duration']     = CountDay::withSearch(['date'], $input)->sum('duration'); 
            $headerCount['duration_avg'] = $headerCount['ip'] == 0 ? '--' : secto_time(ceil($headerCount['duration'] / $headerCount['ip']));
            $headerCount['quit']         = $headerCount['ip'] == 0 ? '--' : round(($headerCount['ip'] / $headerCount['pv']) * 100, 2) . '%';
            $pieData = [];
            $where[] = ['newuser', '<>', '']; 
            $data  = Statistics::withSearch(['date'], $input)
            ->where($where)
            ->field('newuser, count(id) as pv')
            ->group('newuser')
            ->order('pv', 'desc')
            ->select()
            ->toArray();
            foreach ($data as $key => $val) {
                $subWhere[0] = ['newuser', '=', $val['newuser']];
                // 图形
                array_push($pieData, [$val['newuser'], $val['pv']]);
                $categories = [];
                $chartData[$key]['name'] = $val['newuser'];
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

    /**
     * 搜索词排名
     */
    public function keywordRanking()
    {
        if ($this->request->isPost()) {
            $input = input();
            $where[] = ['newuser', '<>', '']; 
            $count = Statistics::withSearch(['date'], $input)->where($where)->group('newuser')->count();
            $data  = Statistics::withSearch(['date'], $input)
            ->where($where)
            ->field('newuser, count(id) as pv')
            ->group('newuser')
            ->order('pv', 'desc')
            ->page($input['page'], $input['pageSize'])
            ->select()
            ->toArray();
            foreach ($data as $key => $val) {
                $subWhere[0] = ['newuser', '=', $val['newuser']];
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
}