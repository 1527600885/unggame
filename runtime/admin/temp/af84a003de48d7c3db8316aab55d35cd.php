<?php /*a:3:{s:60:"/www/wwwroot/www.unicgm.com/app/admin/view/plugins/list.html";i:1663208392;s:61:"/www/wwwroot/www.unicgm.com/app/admin/view/common/header.html";i:1663904303;s:61:"/www/wwwroot/www.unicgm.com/app/admin/view/common/footer.html";i:1650617578;}*/ ?>
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8">
<meta name="viewport" content="width=device-width,minimum-scale=1.0,maximum-scale=1.0,user-scalable=no">
<title>游戏后台管理系统</title>
<link rel="icon" href="/upload/favicon.ico"> 
<link rel="stylesheet" type="text/css" href="/admin/css/element.min.css?v=<?php echo config('app.version'); ?>">
<link rel="stylesheet" type="text/css" href="/admin/css/onekey.min.css?v=<?php echo config('app.version'); ?>">
<script type="text/javascript" src="/admin/js/jquery.min.js?v=<?php echo config('app.version'); ?>"></script>
<script type="text/javascript" src="/admin/js/vue.min.js?v=<?php echo config('app.version'); ?>"></script>
<script type="text/javascript" src="/admin/js/element.min.js?v=<?php echo config('app.version'); ?>"></script>
<script type="text/javascript" src="/admin/js/sortable.min.js?v=<?php echo config('app.version'); ?>"></script>
<script type="text/javascript" src="/admin/js/vuedraggable.min.js?v=<?php echo config('app.version'); ?>"></script>
<script type="text/javascript" src="/admin/js/common.js?v=<?php echo config('app.version'); ?>"></script>
<script type="text/javascript" src="/admin/js/component.js?v=<?php echo config('app.version'); ?>"></script>
<script type="text/javascript" src="/admin/js/nprogress.js?v=<?php echo config('app.version'); ?>"></script>
</head>
<body>
<div id="app" v-cloak>
    <el-container class="el-layout el-plugin">
        <el-aside width="192px">
            <el-tabs v-model="search.install" @tab-click="getData" :tab-position="document.body.clientWidth > 768 ? 'left' : 'top'">
                <el-tab-pane label="插件中心" name="0"></el-tab-pane>
                <el-tab-pane label="已安装" name="1"></el-tab-pane>
                <el-tab-pane label="开发中" name="2"></el-tab-pane>
            </el-tabs>
        </el-aside>
        <el-container>
            <div class="el-content">
                <div class="header">
                    <el-form :inline="true" :model="search" size="small" @submit.native.prevent>
                        <el-form-item>
                            <el-button type="info" icon="el-icon-refresh" @click="searchData()">
                                <span class="title">刷新</span>
                            </el-button>
                        </el-form-item>
                        <el-form-item>
                            <el-input placeholder="关键词搜索" v-model="search.keyword" @keyup.enter.native="searchData()">
                                <el-button slot="append" icon="el-icon-search" @click="searchData()"></el-button>
                            </el-input>
                        </el-form-item>
                    </el-form>
                </div>
                <div class="el-plugin-list">
                    <el-card class="el-plugin-catalog" shadow="hover">
                        <div class="el-plugin-catalog-item">
                            <div class="title">分类:</div>
                            <ul>
                                <li @click="catalogChange('')">
                                    <el-link :underline="false" :class="{active: search.catalog === ''}">全部</el-link>
                                </li>
                                <li v-for="(item, index) in catalog" @click="catalogChange(item.id)">
                                    <el-link :underline="false" :class="{active: search.catalog === item.id}">{{item.title}}</el-link>
                                </li>
                            </ul>
                        </div>
                        <div class="el-plugin-catalog-item">
                            <div class="title">价格:</div>
                            <ul>
                                <li v-for="(item, index) in price" @click="priceChange(item.value)">
                                    <el-link :underline="false" :class="{active: search.price === item.value}">{{item.title}}</el-link>
                                </li>
                            </ul>
                        </div>
                    </el-card>
                    <el-table
                        :data="table" 
                        v-loading="loading" 
                        :default-sort="{prop: search.prop, order: search.order}" 
                        @sort-change="sortChange">
                        <el-table-column prop="plugin" label="插件" width="70">
                            <template slot-scope="scope">
                                <el-image :src="scope.row.c_cover" :row-src-list="[scope.row.c_cover]"></el-image>
                            </template>
                        </el-table-column>
                         <el-table-column prop="plugin" label="" width="150">
                            <template slot-scope="scope">
                                <div>{{scope.row.title}}</div>
                                <div>
                                    <span :style="{color: scope.row.price == 0.00 ? '#409EFF' : '#E6A23C'}">
                                        {{scope.row.price == 0.00 ? '免费' : scope.row.price + '元'}}
                                    </span>
                                </div>
                            </template>
                        </el-table-column>
                        <el-table-column prop="describe" label="描述">
                            <template slot-scope="scope">
                                <div class="el-ellipsis-2"><div v-html="scope.row.describe"></div></div>
                            </template>
                        </el-table-column>
                        <el-table-column prop="user" label="作者" width="150">
                            <template slot-scope="scope">
                                <a :href="scope.row.user.url" target="_blank">{{scope.row.user.nickname}}</a>
                            </template>
                        </el-table-column>
                        <el-table-column prop="install_count" label="下载量" width="100">
                            <template slot-scope="scope">
                                {{scope.row.install_count}}次
                            </template>
                        </el-table-column>
                        <el-table-column prop="status" label="状态" width="100">
                            <template slot-scope="scope">
                                <el-switch
                                    v-if="scope.row.status !== ''"
                                    v-model="scope.row.status"
                                    active-color="#13ce66"
                                    inactive-color="#ff4949"
                                    :active-value="1"
                                    :inactive-value="0"
                                    @change="pluginStatus(scope.row)">
                                </el-switch>
                                <template v-else>未安装</template>
                            </template>
                        </el-table-column>
                        <el-table-column label="操作" width="150">
                            <template slot-scope="scope">
                                <el-tooltip content="详情" placement="top">
                                    <el-button 
                                        icon="el-icon-search" 
                                        size="mini"
                                        @click="pluginDetails(scope.row)"
                                        circle>
                                    </el-button>
                                </el-tooltip>
                                <el-tooltip content="安装" placement="top">
                                    <el-button
                                        v-if="scope.row.install"
                                        :loading="scope.row.installLoading"
                                        size="mini"
                                        icon="el-icon-download"
                                        type="primary" 
                                        @click="pluginInstall(scope.row)"
                                        circle>
                                    </el-button>
                                </el-tooltip>
                                <el-tooltip content="购买并安装" placement="top">
                                    <el-button
                                        v-if="scope.row.shop"
                                        :loading="scope.row.orderLoading"
                                        size="mini"
                                        icon="el-icon-goods"
                                        type="success" 
                                        @click="pluginCreateOrder(scope.row)"
                                        circle>
                                    </el-button>
                                </el-tooltip>
                                <el-tooltip content="更新" placement="top">
                                    <el-button
                                        v-if="scope.row.update"
                                        :loading="scope.row.updateLoading"
                                        size="mini"
                                        icon="el-icon-warning-outline"
                                        type="warning" 
                                        @click="pluginUpdate(scope.row)"
                                        circle>
                                    </el-button>
                                </el-tooltip>
                                <el-tooltip content="卸载" placement="top">
                                    <el-button 
                                        v-if="scope.row.uninstall"
                                        :loading="scope.row.uninstallLoading"
                                        type="danger" 
                                        size="mini" 
                                        icon="el-icon-delete" 
                                        @click="pluginRemove(scope.row)"
                                        circle>
                                    </el-button>
                                </el-tooltip>
                            </template>
                        </el-table-column>
                    </el-table>
                </div>
                <el-pagination
                    @current-change="pageChange"
                    :current-page="search.page"
                    :page-size="15"
                    :total="total"
                    layout="total,prev, pager, next, jumper"
                    background
                    :hide-on-single-page="true">
                </el-pagination>
            </div>
        </el-container>
    </el-container>
    <el-drawer :visible.sync="drawer" :with-header="false" size="100%">
        <el-page-header @back="drawer=false" content="插件详情">
            <template v-slot:title>Esc键返回</template>
        </el-page-header>
        <div class="el-layout">
        <el-tabs tab-position="left" v-if="drawer">
            <el-tab-pane label="插件概览">
                <div class="el-pane-warp el-plugin-single">
                    <div class="el-plugin-info">
                        <el-image :src="row.c_cover" :preview-src-list="[row.c_cover]"></el-image>
                        <p class="line" style="margin-top: 20px">{{row.title}}</p>
                        <p class="line" v-if="orderId != 0">插件订单：{{orderId}}</p>
                        <p class="line" v-else>当前版本：{{row.now_version}}</p>
                        <p class="line">最新版本：{{row.new_version}}</p>
                        <p class="line">最近更新：{{row.update_time}}</p>
                        <p class="line">插件作者：<a :href="row.user.url" target="_blank">{{row.user.nickname}}</a></p>
                        <p class="line">安装次数：{{row.install_count}}</p>
                        <p class="line">插件价格：
                            <span :style="{color: row.price == 0.00 ? '#409EFF' : '#E6A23C'}">
                                {{row.price == 0.00 ? '免费' : row.price + '元'}}
                            </span>
                        </p>
                        <p class="line">插件分类：{{row.catalog}}</p>
                        <p class="line">插件标识：{{row.name}}</p>
                        <p class="line">系统版本：>={{row.c_relyon}}</p>
                        <p class="line" v-if="row.docs != null">相关文档：<a :href="row.docs.url" target="_blank">点击查看</a></p>
                        <p class="line">综合评级：
                            <el-rate
                                style="display: inline-block;"
                                v-model="row.rate"
                                disabled
                                show-score
                                text-color="#ff9900">
                            </el-rate>
                        </p>
                        <p class="line">简短描述：{{row.describe}}</p>
                    </div>
                    <div class="el-plugin-order" v-if="orderId != 0">
                        <div class="qrcode">
                            <div class="warp" v-loading="payLoading">
                                <div v-html="payQrcode"></div>
                                <div class="expired" v-if="payStatus === 0"></div>
                                <div class="success" v-if="payStatus === 2"></div>
                            </div>
                            <div class="bottom">
                                <i class="el-icon-full-screen"></i>
                                <span>请使用{{payMethod == 0 ? '支付宝' : '微信'}}扫一扫二维码支付</span>
                            </div>
                        </div>
                        <ul class="method">
                            <li :class="{active: payMethod == 0}" @click="payMethodChange(0)"><img src="/admin/images/pay-ali.png"></li>
                            <li :class="{active: payMethod == 1}" @click="payMethodChange(1)"><img src="/admin/images/pay-wechat.png"></li>
                        </ul>
                        <a href="<?php echo config('app.api'); ?>/agreement-plugins" target="_blank">使用前请仔细阅读《协议说明》</a>
                    </div>
                </div>
            </el-tab-pane>
            <el-tab-pane label="图文描述">
                <div class="el-pane-warp">
                    <div v-html="row.content"></div>
                </div>
            </el-tab-pane>
            <el-tab-pane label="版本修订">
                <div class="el-pane-warp">
                    <el-timeline>
                        <el-timeline v-for="(item, index) in row.version_list">
                            <el-timeline-item :timestamp="item.create_time" placement="top">
                                <el-card>
                                    <h4 style="margin-bottom: 10px">{{item.c_version}}</h4>
                                    <div v-html="item.describe"></div>
                                </el-card>
                            </el-timeline-item>
                        </el-timeline>
                    </el-timeline>
                </div>
            </el-tab-pane>
        </el-tabs>
    </div>
    </el-drawer>
</div>
<script>
    var plugins = new Vue({
        el: '#app',
        data() {
            return {
                row: {},
                table: [],
                drawer:false,   
                loading: false,
                search:{
                    keyword: '',
                    page: 1,
                    prop: '', 
                    order: '',
                    price: '',
                    catalog: '',
                    install: '0',
                },
                total: 0,
                price:[{title: '全部', value: ''},{title: '免费', value: 'free'},{title: '付费', value: 'charge'}],
                catalog: <?php echo json_encode($catalog); ?>,
                indexUrl: 'plugins/list',
                updateUrl: 'plugins/update',
                installUrl: 'plugins/install',
                updateInstallUrl: 'plugins/updateInstall',
                deleteUrl: 'plugins/delete',
                orderId: 0,
                createOrderUrl: 'plugins/createOrder',
                orderStatusUrl: 'plugins/statusOrder',
                payMethod: 0,
                payQrcode: '',
                payStatus: '',
                payLoading: false,
                payMethodUrl: 'plugins/payMethod',
                timer: null,
            }
        },
        created () {
            this.getData();
        },
        methods: {
            /**
             * 获取数据
             */
            getData() {
                let self = this;
                self.loading = true;
                self.search.order = self.search.order === 'ascending' ? 'asc' : 'desc';
                request.post(self.indexUrl, self.search, function(res) {
                    self.loading = false;
                    if (res.status === 'success') {
                        self.table = res.data;
                        self.total = res.count;
                        parent.parentVm.menu = res.publicMenu;
                    } else {
                        self.$notify({ showClose: true, message: res.message, type: res.status});
                    }
                });
            },
            /**
             * 查看详情
             * @param  {Object} row 当前行
             */
            pluginDetails(row) {
                this.row = row;
                this.drawer = true;
            },
            /**
             * 状态管理
             * @param  {Object} row 当前行
             */
            pluginStatus(row) {
                let self = this;
                request.post(self.updateUrl, {name: row.name, status: row.status}, function(res) {
                    if (res.status === 'success') {
                        self.getData(); 
                    } else {
                        self.$message({ showClose: true, message: res.message, type: res.status});
                    }
                });
            },
            /**
             * 插件购买
             * @param  {Object} row 当前行
             */
            pluginCreateOrder(row) {
                let self = this;
                row.orderLoading = true;
                request.post(self.createOrderUrl, {name: row.name}, function(res) {
                    if (res.status === 'success') {
                        self.orderId   = res.data.id;
                        self.pluginDetails(row);
                        self.pluginStatusOrder(row);
                        self.payMethodChange();
                    } else {
                        self.$message({ showClose: true, message: res.message, type: res.status});
                    }
                    row.orderLoading = false;
                });
            },
            /**
             * 插件订单状态
             * @param  {Object} row 当前行
             */
            pluginStatusOrder(row) {
                let self = this;
                self.timer = setInterval(() => {
                    request.post(self.orderStatusUrl, {id: self.orderId}, function(res) {
                        if (res.status === 'success') {
                            self.payStatus = res.data;
                            if (self.payStatus === 2) {
                                self.pluginInstall(row);
                                setTimeout(() => {
                                    self.drawer = false;
                                }, 1000);
                            }
                        }
                    });
                }, 2000)
            },
            /**
             * 支付方式
             * @param  {Object} row 当前行
             */
            payMethodChange(payMethod = 0) {
                let self = this;
                self.payMethod = payMethod;
                self.payLoading = true;
                request.post(self.payMethodUrl, {id: self.orderId, method: self.payMethod}, function(res) {
                    if (res.status === 'success') {
                        self.payQrcode = res.data;
                    } else {
                        self.$message({ showClose: true, message: res.message, type: res.status});
                    }
                    self.payLoading = false;
                });
            },
            /**
             * 插件安装
             * @param  {Object} row 当前行
             */
            pluginInstall(row) {
                let self = this;
                row.installLoading = true;
                request.post(self.installUrl, {name: row.name}, function(res) {
                    if (res.status === 'success') {
                        self.getData(); 
                    } else {
                        self.$message({ showClose: true, message: res.message, type: res.status});
                    }
                    row.installLoading = false;
                });
            },
            /**
             * 插件更新
             * @param  {Object} row 当前行
             */
            pluginUpdate(row) {
                let self = this;
                row.updateLoading = true;
                request.post(self.updateInstallUrl, {name: row.name}, function(res) {
                    if (res.status === 'success') {
                        self.getData(); 
                    } else {
                        self.$message({ showClose: true, message: res.message, type: res.status});
                    }
                    row.updateLoading = false;
                });
            },
            /**
             * 插件卸载
             * @param  {Object} row 当前行
             */
            pluginRemove(row) {
                let self = this;
                self.$confirm('确定卸载'+row.title+'插件吗？', '', {
                    confirmButtonText: '确定',
                    cancelButtonText: '取消',
                    type: 'warning'
                }).then(() => {
                    row.uninstallLoading = true;
                    request.post(self.deleteUrl, {name: row.name}, function(res){
                        if (res.status === 'success') {
                            self.getData();
                        } else {
                            self.$message({ showClose: true, message: res.message, type: res.status});
                        }
                        row.uninstallLoading = false;
                    });
                }).catch(() => {});
            },
            /**
             * 排序
             */
            sortChange(val) {
                this.search = Object.assign({}, this.search, {prop: val.prop , order: val.order});
            },
            /**
             * 关键词/日期搜索
             */
            searchData() {
                this.search = Object.assign({}, this.search, {page: 1});
            },
            /**
             * 分类搜索
             * @param  {Object} val 搜索内容
             */
            catalogChange(val) {
                this.search = Object.assign({}, this.search, {page: 1, catalog: val});
            },
            /**
             * 分页改变时
             * @param  {String} val 当前页
             */
            pageChange(val) {
                this.search = Object.assign({}, this.search, {page: val});
            },
            /**
             * 价格类目搜索
             * @param  {Object} val 搜索内容
             */
            priceChange(val) {
                this.search = Object.assign({}, this.search, {page: 1, price: val});
            },
        },
        watch: {
            search() {
                this.getData();
            },
            drawer(v) {
                if (!v) {
                    this.orderId = 0;
                    clearInterval(this.timer);
                }
            }
        }
    })
</script>
</body>
</html>