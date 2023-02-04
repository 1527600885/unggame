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
 * 总览
 */
class Index extends BaseController
{
    public function index()
    {
        $input = input();
        // 今日
        $list  = [];
        $day = CountDay::whereDay('day')->withoutField('day')->find();
        $list[0] = $day ? $day->toArray() : [];
        $list[0]['title'] = "今日";
        // 昨日
        $yesterday = CountDay::whereDay('day', 'yesterday')->withoutField('day')->find();
        $list[1] = $yesterday ? $yesterday->toArray() : [];
        $list[1]['title'] = "昨日";
        // 昨日此时
        $yesterdayMorning = strtotime(date('Y-m-d',strtotime('-1 day')));
        $yesterdayTime    = strtotime('-1 day');
        $list[2]['ip']       = Statistics::whereTime('create_time', 'between', [$yesterdayMorning, $yesterdayTime])->group('ip')->count();
        $list[2]['pv']       = Statistics::whereTime('create_time', 'between', [$yesterdayMorning, $yesterdayTime])->count();
        $list[2]['uv']       = Statistics::whereTime('create_time', 'between', [$yesterdayMorning, $yesterdayTime])->group('ip,user_id')->count();
        $list[2]['duration'] = Statistics::whereTime('create_time', 'between', [$yesterdayMorning, $yesterdayTime])->sum('duration');
        $list[2]['title']    = "昨日此时";
        // 预计今日
        $list[3]['ip']    = 0;
        $list[3]['pv']    = 0;
        $list[3]['uv']    = 0;
        $list[3]['title'] = "预计今日";
        // 历史顶峰
        $historicalPeak = CountDay::order('pv','desc')->withoutField('day')->find();
        $list[4] = $historicalPeak ? $historicalPeak->toArray() : [];
        $list[4]['title'] = "历史顶峰";
        // 每日平均(需要防止出现某天无流量的情况)
        $list[5]['ip']       = (int)CountDay::avg('ip');
        $list[5]['pv']       = (int)CountDay::avg('pv');
        $list[5]['uv']       = (int)CountDay::avg('uv');
        $list[5]['duration'] = CountDay::avg('duration');
        $list[5]['title']    = "每日平均";
        foreach ($list as $key => $val) {
            $list[$key]['ip'] = isset($val['ip']) ? $val['ip'] : 0;
            $list[$key]['pv'] = isset($val['pv']) ? $val['pv'] : 0;
            $list[$key]['uv'] = isset($val['uv']) ? $val['uv'] : 0;
            $list[$key]['duration'] = isset($val['duration']) ? $val['duration'] : 0;
            $list[$key]['duration_avg'] = '--';
            $list[$key]['quit'] = '--';
            if ($list[$key]['ip'] !== 0 && $key !== 3) {
                // 平均访问时长
                $list[$key]['duration_avg'] = secto_time(ceil($list[$key]['duration'] / $list[$key]['ip']));
                // 跳出率
                $list[$key]['quit'] = round(($list[$key]['ip'] / $list[$key]['pv']) * 100, 2) . '%';
            }
        }
        // 预计今日
        $list[3]['ip_increase'] = ($list[0]['ip'] !== 0 && $list[2]['ip'] !== 0) ? round($list[0]['ip'] / $list[2]['ip'], 2) : 0; // 同比增长/减少
        $list[3]['pv_increase'] = ($list[0]['pv'] !== 0 && $list[2]['pv'] !== 0) ? round($list[0]['pv'] / $list[2]['pv'], 2) : 0; // 同比增长/减少
        $list[3]['uv_increase'] = ($list[0]['uv'] !== 0 && $list[2]['uv'] !== 0) ? round($list[0]['uv'] / $list[2]['uv'], 2) : 0; // 同比增长/减少
        $list[3]['ip'] = $list[0]['ip'] + ceil( $list[3]['ip_increase'] * ($list[1]['ip'] - $list[2]['ip']) );
        $list[3]['pv'] = $list[0]['pv'] + ceil( $list[3]['pv_increase'] * ($list[1]['pv'] - $list[2]['pv']) );
        $list[3]['uv'] = $list[0]['uv'] + ceil( $list[3]['uv_increase'] * ($list[1]['uv'] - $list[2]['uv']) );
        View::assign('headerTable', $list);
        return View::fetch();
    }

    /**
     * 单图形
     */
    public function chart()
    {
        if ($this->request->isPost()) {
            $input = input();
            $categories = [];
            $data       = [];
            switch ($input['type']) {
                // 趋势图
                case 'trend':
                        $data[0]['name'] = "浏览量(PV)";
                        $data[0]['data'] = [];
                        $data[1]['name'] = "访客数(UV)";
                        $data[1]['data'] = [];
                        switch ($input['time']) {
                            case 'today':
                            case 'yesterday':
                                $day = $input['time'] == 'today' ? date("Y-m-d") : date("Y-m-d",strtotime("-1 day"));
                                for ($i=0; $i < 24; $i++) {
                                    $time = (str_pad($i, 2, 0, STR_PAD_LEFT));
                                    $cate = $time . ':00 - ' . $time . ':59';
                                    array_push($categories, $cate);
                                    $start = strtotime($day.' '.$time . ':00:00');
                                    $end   = strtotime($day.' '.$time . ':59:59');
                                    $pv = Statistics::whereTime('create_time', 'between', [$start, $end])->count();
                                    $uv = Statistics::whereTime('create_time', 'between', [$start, $end])->group('ip,user_id')->count();
                                    array_push($data[0]['data'], $pv);
                                    array_push($data[1]['data'], $uv);
                                }
                                break;
                            case 'week':
                            case 'month':
                                $day = $input['time'] == 'week' ? 7 : 30;
                                for ($i = 0; $i < $day; $i++) {
                                    $cate = date("m-d", strtotime("-". $i ." day"));
                                    array_push($categories, $cate);
                                    $whereDay  = date("Y-m-d", strtotime("-". $i ." day"));
                                    $pv = Statistics::whereDay('create_time', $whereDay)->count();
                                    $uv = Statistics::whereDay('create_time', $whereDay)->group('ip,user_id')->count();
                                    array_push($data[0]['data'], $pv);
                                    array_push($data[1]['data'], $uv);
                                }
                                break;
                        }
                    break;
                // 访问地区 / 浏览器
                case 'broswer':
                case 'province':
                        switch ($input['time']) {
                            case 'today':
                            case 'yesterday':
                                if ($input['time'] == 'today') {
                                    $start = strtotime(date('Y-m-d', strtotime("-1 day")));
                                    $end   = time();
                                } else {
                                    $start = date('Y-m-d', strtotime("-1 day")). '00:00:00';
                                    $end   = date('Y-m-d', strtotime("-1 day")). '23:59:59';
                                }
                                break;
                            case 'week':
                            case 'month':
                                $day   = $input['time'] == 'week' ? 7 : 30;
                                $start = strtotime(date('Y-m-d', strtotime('-'.$day.' day')));
                                $end   = time();
                                break;
                        }
                        $list  = Statistics::whereTime('create_time', 'between', [$start, $end])
                        ->where($input['type'], '<>', '')
                        ->field($input['type'] . ', count(id) as pv')
                        ->group($input['type'])
                        ->order('pv', 'desc')
                        ->select()
                        ->toArray();
                        if ($input['type'] === 'province') {
                            foreach ($list as $k => $v) {
                                $data[$k]['name'] = mb_substr($v['province'], 0, -1);;
                                $data[$k]['value'] = $v['pv'];
                            }
                        } else {
                            foreach ($list as $k => $v) {
                                $data[$k][0] = $v['broswer'];
                                $data[$k][1] = $v['pv'];
                            }
                        }
                    break;
            }
            return json(['status' => 'success', 'message' => '获取成功', 'data' => $data , 'categories' => $categories]);
        }
    }
    /**
     * 单表格
     */
    public function table()
    {
        if ($this->request->isPost()) {
            $input = input();
            $type  = $input['type'];
            $time  = $input['time'];
            $where = [];
            // 入口页面
            if($type == 'entryUrl'){
                $type = 'url';
                $where[] = ['referrer', '<>', ''];
            }
            switch ($time) {
                case 'today':
                case 'yesterday':
                    if ($time == 'today') {
                        $start = strtotime(date('Y-m-d', strtotime("-1 day")));
                        $end   = time();
                    } else {
                        $start = date('Y-m-d', strtotime("-1 day")). '00:00:00';
                        $end   = date('Y-m-d', strtotime("-1 day")). '23:59:59';
                    }
                    break;
                case 'week':
                case 'month':
                    $day   = $time == 'week' ? 7 : 30;
                    $start = strtotime(date('Y-m-d', strtotime('-'.$day.' day')));
                    $end   = time();
                    break;
            }
            $data = Statistics::whereTime('create_time', 'between', [$start, $end])
            ->where($where)
            ->field($type . ' as name, count(id) as pv')
            ->group($type)
            ->order('pv', 'desc')
            ->limit(10)
            ->select()
            ->toArray();
            $count = array_sum(array_map(function($val){return $val['pv'];}, $data));
            foreach ($data as $k => $v) {
                $data[$k]['name'] = $v['name'] === '' ? '直接访问' : $v['name'];
                $data[$k]['percentage'] = round(($v['pv'] / $count), 3) *100 . '%';
            }
            return json(['status' => 'success', 'message' => '获取成功', 'data' => $data]);
        }
    }
    /**
     * 新用户统计
     */
    public function newuser(){
        if ($this->request->isPost()) {
            $input = input();
            $time  = $input['time'];
            $data  = [];
            switch ($time) {
                case 'today':
                case 'yesterday':
                    if ($time == 'today') {
                        $start = strtotime(date('Y-m-d', strtotime("-1 day")));
                        $end   = time();
                    } else {
                        $start = date('Y-m-d', strtotime("-1 day")). '00:00:00';
                        $end   = date('Y-m-d', strtotime("-1 day")). '23:59:59';
                    }
                    break;
                case 'week':
                case 'month':
                    $day   = $time == 'week' ? 7 : 30;
                    $start = strtotime(date('Y-m-d', strtotime('-'.$day.' day')));
                    $end   = time();
            }
            $data[0]['title'] = "浏览量";
            $data[0]['old']   = Statistics::whereTime('create_time', 'between', [$start, $end])->where('newuser', 0)->count();
            $data[0]['new']   = Statistics::whereTime('create_time', 'between', [$start, $end])->where('newuser', 1)->count();
            $data[1]['title'] = "访客数";
            $data[1]['old']   = Statistics::whereTime('create_time', 'between', [$start, $end])->where('newuser', 0)->group('ip,user_id')->count();
            $data[1]['new']   = Statistics::whereTime('create_time', 'between', [$start, $end])->where('newuser', 1)->group('ip,user_id')->count();
            $data[2]['title'] = "IP数";
            $data[2]['old']   = Statistics::whereTime('create_time', 'between', [$start, $end])->where('newuser', 0)->group('ip')->count();
            $data[2]['new']   = Statistics::whereTime('create_time', 'between', [$start, $end])->where('newuser', 1)->group('ip')->count();
            $data[3]['title'] = "跳出率";
            $data[3]['old']   = $data[0]['old'] == 0 ? '0%' : round(($data[2]['old'] / $data[0]['old']) * 100, 2) . '%';
            $data[3]['new']   = $data[0]['new'] == 0 ? '0%' : round(($data[2]['new'] / $data[0]['new']) * 100, 2) . '%';
            $count = $data[0]['old'] + $data[0]['new'];
            $header[0]['cover'] = '';
            $header[0]['old']   = $data[0]['old'] == 0 ? 0 : round(($data[0]['old'] / $count) * 100, 2);
            $header[0]['new']   = $data[0]['new'] == 0 ? 0 : round(($data[0]['new'] / $count) * 100, 2);
            return json(['status' => 'success', 'message' => '获取成功', 'data' => $data, 'header' => $header]);
        }
    }
}