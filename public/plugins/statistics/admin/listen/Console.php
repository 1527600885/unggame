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

use app\api\model\v2\Order;
use app\api\model\Withdrawal;
use plugins\statistics\admin\model\CountDay;
use app\admin\model\User;

class Console
{
    public function handle($object)
    {
//        $countPv  = 0;
//        $countIp  = 0;
        $countReg = User::count();
        $dayReg   = User::whereDay('login_time')->count();
        $monthReg = User::whereMonth("login_time")->count();
        $recharge = round(Order::where("status",1)->sum("realAmount"),2);
        $widthdraw =  round(Withdrawal::where("online_status",2)->sum("amount"),2);
        $waitcheck = Withdrawal::where("online_status",0)->count();
        $chargeReg =  round(Withdrawal::where("online_status",2)->sum("charge_doller"),2);
//        $count    = CountDay::whereDay('day')->find();
//        if ($count) {
//            $countPv = $count['pv'];
//            $countIp = $count['ip'];
//        }
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
                            <dt style="border-color:#00bcd4"><i class="info-item-ico ico-count-num"></i><span class="info-item-txt">总会员数</span></dt>
                            <dd style="color:#00bcd4">'.$countReg.'</dd>
                            </dl>
                        </el-col>
                        <el-col :sm="12" :md="6">
                            <dl>
                            <dt><i class="info-item-ico ico-count-pv"></i><span class="info-item-txt">今日活跃数量</span></dt>
                            <dd>'.$dayReg.'</dd>
                            </dl>
                        </el-col>
                        <el-col :sm="12" :md="6">
                            <dl>
                            <dt style="border-color:#00c29f"><i class="info-item-ico ico-count-uv"></i><span class="info-item-txt">本月活跃数量</span></dt>
                            <dd style="color:#00c29f">'.$monthReg.'</dd>
                            </dl>
                        </el-col>
                        <el-col :sm="12" :md="6">
                            <dl>
                            <dt style="border-color:#fb9678"><i class="info-item-ico ico-count-other"></i><span class="info-item-txt">充值总额</span></dt>
                            <dd style="color:#fb9678">'.$recharge.'</dd>
                            </dl>
                        </el-col>
                         <el-col :sm="12" :md="6">
                            <dl>
                            <dt style="border-color:#fb9678"><i class="info-item-ico ico-count-other"></i><span class="info-item-txt">提现总额</span></dt>
                            <dd style="color:#fb9678">'.$widthdraw.'</dd>
                            </dl>
                        </el-col>
                         <el-col :sm="12" :md="6">
                            <dl>
                            <dt style="border-color:#fb9678"><i class="info-item-ico ico-count-other"></i><span class="info-item-txt">手续费总额</span></dt>
                            <dd style="color:#fb9678">'.$chargeReg.'</dd>
                            </dl>
                        </el-col>
                         <el-col :sm="12" :md="6">
                            <dl>
                            <dt style="border-color:#fb9678"><i class="info-item-ico ico-count-other"></i><span class="info-item-txt">待审核数量</span></dt>
                            <dd style="color:#fb9678">'.$waitcheck.'</dd>
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
//        <el-col :md="24" :lg="12">
//                            <el-statistics-overview-chart
//                                chart="province"
//                                title="地域分布"
//                                type="province"
//                                link="statistics/visitor/area"
//                                :time="timeActive">
//                            </el-statistics-overview-chart>
//                        </el-col>
//                        <el-col :md="24" :lg="12">
//                            <el-statistics-overview-table
//                                title="受访页面"
//                                type="url"
//                                link="statistics/visit/url"
//                                :time="timeActive">
//                            </el-statistics-overview-table>
//                        </el-col>
//                        <el-col :md="24" :lg="12">
//                            <el-statistics-overview-table
//                                title="入口页面"
//                                type="entryUrl"
//                                link="statistics/visit/entryUrl"
//                                :time="timeActive">
//                            </el-statistics-overview-table>
//                        </el-col>
//                        <el-col :md="24" :lg="12">
//                            <el-statistics-overview-table
//                                title="来源网站"
//                                type="referrer"
//                                link="statistics/source/referrer"
//                                :time="timeActive">
//                            </el-statistics-overview-table>
//                        </el-col>
    }   
}