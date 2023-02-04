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

use app\admin\BaseController;
use think\facade\View;
use plugins\statistics\admin\model\CountDay;
use plugins\statistics\admin\model\Statistics;
/**
 * 来源分析
 */
class Source extends BaseController
{
	/**
	 * 全部来源
	 */
    public function all()
    {
    	if ($this->request->isPost()) {
            $input = input();
            $count = Statistics::withSearch(['date'], $input)->group('referrer')->count();
            $data  = Statistics::withSearch(['date'], $input)
            ->field('referrer, count(id) as pv')
            ->group('referrer')
            ->order('pv', 'desc')
            ->page($input['page'], $input['pageSize'])
            ->select()
            ->toArray();
            foreach ($data as $key => $val) {
            	$data[$key]['ip'] = Statistics::withSearch(['date'], $input)->where('referrer', $val['referrer'])->group('ip')->count();
            	$data[$key]['uv'] = Statistics::withSearch(['date'], $input)->where('referrer', $val['referrer'])->group('ip,user_id')->count();
            	$data[$key]['duration'] = Statistics::withSearch(['date'], $input)->where('referrer', $val['referrer'])->sum('duration');
                $data[$key]['duration_avg'] = $data[$key]['ip'] == 0 ? '--' : secto_time(ceil($data[$key]['duration'] / $data[$key]['ip']));
                $data[$key]['quit'] = $data[$key]['ip'] == 0 ? '--' : round(($data[$key]['ip'] / $data[$key]['pv']) * 100, 2) . '%';
                $data[$key]['referrer'] = empty($val['referrer']) ? '直接访问' : $val['referrer'];
            }
            return json([
                'status'  => 'success', 
                'message' => '获取成功', 
                'count'   => $count,
                'data'    => $data
            ]);
    	} else {
    		return View::fetch();
    	}
    }

    /**
     * 全部来源图表
     */
    public function allChart()
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
            $data  = Statistics::withSearch(['date'], $input)
            ->field('referrer, count(id) as pv')
            ->group('referrer')
            ->order('pv', 'desc')
            ->select()
            ->toArray();
            $categories = [];
            $chartData  = [];
            foreach ($data as $key => $val) {
                $data[$key]['referrer'] = empty($val['referrer']) ? '直接访问' : $val['referrer'];
                array_push($pieData, [$data[$key]['referrer'], $val['pv']]);
                $chartData[$key]['name'] = $data[$key]['referrer'];
                $chartData[$key]['data'] = [];
                $interval = (strtotime($input['date'][1]) - strtotime($input['date'][0])) / (3600 * 24);
                $dateArr  = array_column($data, 'day');
                for ($i = 0; $i < $interval; $i++) { 
                    $time = strtotime($input['date'][0]) + (3600 * 24 * $i);
                    $date = date('Y-m-d', $time);
                    array_push($categories, $date);
                    array_push($chartData[$key]['data'], Statistics::whereDay('create_time', $date)->where('referrer', $val['referrer'])->count());
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
     * 搜索引擎
     */
    public function keywordForm()
    {
        if ($this->request->isPost()) {
            $input = input();
            $where[] = ['keyword_from', '<>', '']; 
            $count = Statistics::withSearch(['date'], $input)->where($where)->group('keyword_from')->count();
            $data  = Statistics::withSearch(['date'], $input)
            ->where($where)
            ->field('keyword_from, count(id) as pv')
            ->group('keyword_from')
            ->order('pv', 'desc')
            ->page($input['page'], $input['pageSize'])
            ->select()
            ->toArray();
            foreach ($data as $key => $val) {
                $subWhere[0] = ['keyword_from', '=', $val['keyword_from']];
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
     * 搜索引擎图表
     */
    public function keywordFormChart()
    {
        if ($this->request->isPost()) {
            $input = input();
            $headerCount['ip']           = Statistics::withSearch(['date'], $input)->where('keyword_from','<>','')->group('ip')->count();
            $headerCount['pv']           = Statistics::withSearch(['date'], $input)->where('keyword_from','<>','')->count();
            $headerCount['uv']           = Statistics::withSearch(['date'], $input)->where('keyword_from','<>','')->group('ip,user_id')->count();
            $headerCount['duration']     = Statistics::withSearch(['date'], $input)->where('keyword_from','<>','')->sum('duration'); 
            $headerCount['duration_avg'] = $headerCount['ip'] == 0 ? '--' : secto_time(ceil($headerCount['duration'] / $headerCount['ip']));
            $headerCount['quit']         = $headerCount['ip'] == 0 ? '--' : round(($headerCount['ip'] / $headerCount['pv']) * 100, 2) . '%';
            $pieData = [];
            $where[] = ['keyword_from', '<>', '']; 
            $data  = Statistics::withSearch(['date'], $input)
            ->where($where)
            ->field('keyword_from, count(id) as pv')
            ->group('keyword_from')
            ->order('pv', 'desc')
            ->select()
            ->toArray();
            $categories = [];
            $chartData  = [];
            foreach ($data as $key => $val) {
                $subWhere[0] = ['keyword_from', '=', $val['keyword_from']];
                // 图形
                array_push($pieData, [$val['keyword_from'], $val['pv']]);
                $chartData[$key]['name'] = $val['keyword_from'];
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
     * 搜索词
     */
    public function keyword()
    {
        if ($this->request->isPost()) {
            $input = input();
            $where[] = ['keyword', '<>', '']; 
            $count = Statistics::withSearch(['date'], $input)->where($where)->group('keyword')->count();
            $data  = Statistics::withSearch(['date'], $input)
            ->where($where)
            ->field('keyword, count(id) as pv')
            ->group('keyword')
            ->order('pv', 'desc')
            ->page($input['page'], $input['pageSize'])
            ->select()
            ->toArray();
            foreach ($data as $key => $val) {
                $subWhere[0] = ['keyword', '=', $val['keyword']];
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
     * 搜索词图表
     */
    public function keywordChart()
    {
        if ($this->request->isPost()) {
            $input = input();
            $headerCount['ip']           = Statistics::withSearch(['date'], $input)->where('keyword','<>','')->group('ip')->count();
            $headerCount['pv']           = Statistics::withSearch(['date'], $input)->where('keyword','<>','')->count();
            $headerCount['uv']           = Statistics::withSearch(['date'], $input)->where('keyword','<>','')->group('ip,user_id')->count();
            $headerCount['duration']     = Statistics::withSearch(['date'], $input)->where('keyword','<>','')->sum('duration');
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
     * 外部链接
     */
    public function referrer()
    {
        if ($this->request->isPost()) {
            $input = input();
            $where[] = ['referrer', '<>', '']; 
            $count = Statistics::withSearch(['date'], $input)->where($where)->group('referrer')->count();
            $data  = Statistics::withSearch(['date'], $input)
            ->where($where)
            ->field('referrer, count(id) as pv')
            ->group('referrer')
            ->order('pv', 'desc')
            ->page($input['page'], $input['pageSize'])
            ->select()
            ->toArray();
            foreach ($data as $key => $val) {
                $subWhere[0] = ['referrer', '=', $val['referrer']];
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
     * 外部链接图表
     */
    public function referrerChart()
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
            ->field('referrer, count(id) as pv')
            ->group('referrer')
            ->order('pv', 'desc')
            ->select()
            ->toArray();
            $categories = [];
            $chartData  = [];
            foreach ($data as $key => $val) {
                $subWhere[0] = ['referrer', '=', $val['referrer']];
                // 图形
                array_push($pieData, [$val['referrer'], $val['pv']]);
                $chartData[$key]['name'] = $val['referrer'];
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