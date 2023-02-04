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
namespace plugins\statistics\admin\listen;

use plugins\statistics\admin\model\CountDay;
use app\admin\model\User;

class Console
{
    public function handle($object)
    {
        $countPv  = 0;
        $countIp  = 0;
        $countReg = User::count();
        $dayReg   = User::whereDay('create_time')->count();
        $count    = CountDay::whereDay('day')->find();
        if ($count) {
            $countPv = $count['pv'];
            $countIp = $count['ip'];
        }
        $object->html .= '
            <link rel="stylesheet" type="text/css" href="/plugins/statistics/static/admin/css/statistics.css">
            <script type="text/javascript" charset="utf-8" src="/plugins/statistics/static/admin/js/statistics.js"></script>
            <script type="text/javascript" charset="utf-8" src="/plugins/statistics/static/admin/js/highmaps.js"></script>
            <script type="text/javascript" charset="utf-8" src="/plugins/statistics/static/admin/js/china.js"></script>
            <script type="text/javascript" charset="utf-8" src="/plugins/statistics/static/admin/js/world.js"></script>
            <div class="el-warp">
                <div id="statistics">
                    <el-row class="el-floor" :getter="10">
                        <el-col :sm="12" :md="6">
                            <dl>
                            <dt><i class="info-item-ico ico-count-pv"></i><span class="info-item-txt">今日浏览量(PV)</span></dt>
                            <dd>'.$countPv.'</dd>
                            </dl>
                        </el-col>
                        <el-col :sm="12" :md="6">
                            <dl>
                            <dt style="border-color:#00c29f"><i class="info-item-ico ico-count-uv"></i><span class="info-item-txt">今日浏览量(IP)</span></dt>
                            <dd style="color:#00c29f">'.$countIp.'</dd>
                            </dl>
                        </el-col>
                        <el-col :sm="12" :md="6">
                            <dl>
                            <dt style="border-color:#00bcd4"><i class="info-item-ico ico-count-num"></i><span class="info-item-txt">总会员数</span></dt>
                            <dd style="color:#00bcd4">'.$countReg.'</dd>
                            </dl>
                        </el-col>
                        <el-col :sm="12" :md="6">
                            <dl>
                            <dt style="border-color:#fb9678"><i class="info-item-ico ico-count-other"></i><span class="info-item-txt">今日注册</span></dt>
                            <dd style="color:#fb9678">'.$dayReg.'</dd>
                            </dl>
                        </el-col>
                    </el-row>
                    <el-row :gutter="20" class="el-statistics-main el-console-content">
                        <el-col :md="24" :lg="12">
                            <el-statistics-overview-chart
                                chart="areaspline"
                                title="趋势图" 
                                type="trend"
                                link="statistics/flow/trend"
                                :time="timeActive">
                            </el-statistics-overview-chart>
                        </el-col>
                        <el-col :md="24" :lg="12">
                            <el-statistics-overview-chart
                                chart="pie"
                                title="访问终端" 
                                type="broswer"
                                link="statistics/visitor/os"
                                :time="timeActive">
                            </el-statistics-overview-chart>
                        </el-col>
                        <el-col :md="24" :lg="12">
                            <el-statistics-overview-chart
                                chart="province"
                                title="地域分布" 
                                type="province"
                                link="statistics/visitor/area"
                                :time="timeActive">
                            </el-statistics-overview-chart>
                        </el-col>
                        <el-col :md="24" :lg="12">
                            <el-statistics-overview-table
                                title="受访页面" 
                                type="url" 
                                link="statistics/visit/url"
                                :time="timeActive">
                            </el-statistics-overview-table>
                        </el-col>
                        <el-col :md="24" :lg="12">
                            <el-statistics-overview-table
                                title="入口页面" 
                                type="entryUrl" 
                                link="statistics/visit/entryUrl"
                                :time="timeActive">
                            </el-statistics-overview-table>
                        </el-col>
                        <el-col :md="24" :lg="12">
                            <el-statistics-overview-table
                                title="来源网站" 
                                type="referrer" 
                                link="statistics/source/referrer"
                                :time="timeActive">
                            </el-statistics-overview-table>
                        </el-col>
                    </el-row>
                </div>
            </div>
            <script>
            new Vue({
                el: "#statistics",
                data() {
                    return {
                        timeActive: "today",
                    }
                },
            })
            </script>
        ';
    }   
}