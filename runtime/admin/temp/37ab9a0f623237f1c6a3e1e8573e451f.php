<?php /*a:3:{s:64:"/www/wwwroot/game.uswindltd.com/app/admin/view/themes/index.html";i:1663579618;s:65:"/www/wwwroot/game.uswindltd.com/app/admin/view/common/header.html";i:1663904303;s:65:"/www/wwwroot/game.uswindltd.com/app/admin/view/common/footer.html";i:1650617578;}*/ ?>
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
    <el-container class="el-layout el-theme">
        <el-aside width="192px">
            <el-tabs v-model="search.install" @tab-click="getData" :tab-position="document.body.clientWidth > 768 ? 'left' : 'top'">
                <el-tab-pane label="主题中心" name="0"></el-tab-pane>
                <el-tab-pane label="已安装" name="1"></el-tab-pane>
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
                <div class="el-theme-list">
                    <el-card class="el-theme-catalog" shadow="hover">
                        <div class="el-theme-catalog-item">
                            <div class="title">分类：</div>
                            <ul>
                                <li @click="catalogChange('')">
                                    <el-link :underline="false" :class="{active: search.catalog === ''}">全部</el-link>
                                </li>
                                <li v-for="(item, index) in catalog" @click="catalogChange(item.id)">
                                    <el-link :underline="false" :class="{active: search.catalog === item.id}">{{item.title}}</el-link>
                                </li>
                            </ul>
                        </div>
                        <div class="el-theme-catalog-item">
                            <div class="title">价格：</div>
                            <ul>
                                <li v-for="(item, index) in price" @click="priceChange(item.value)">
                                    <el-link :underline="false" :class="{active: search.price === item.value}">{{item.title}}</el-link>
                                </li>
                            </ul>
                        </div>
                        <div class="el-theme-catalog-item" style="margin-top: 10px">
                            主题：
                            <el-select size="small" v-model="theme" @change="themeChange()">
                                <el-option v-for="(item, index) in install" :label="item.title" :value="item.name">
                                    {{item.title}}<span style="float: right;color: #999">{{item.name}}</span>
                                </el-option>
                            </el-select>
                            切换
                            <el-tooltip content="安装主题后可自由切换主题，主题标签在系统配置里，修改内容无需修改代码！" placement="top">
                                <i style="color:#F56C6C;font-size:18px;margin-left: 5px;" class="el-icon-question"></i>
                            </el-tooltip>
                        </div>
                    </el-card>
                    <div style="el-themes" v-loading="loading">
                        <el-empty v-if="table.length == 0" description="暂无模板"></el-empty>
                        <div class="el-themes-item" v-for="(item, index) in table">
                            <img v-if="item.use" class="use" src="/admin/images/use.png">
                            <i class="el-themes-count">{{item.install_count}}次安装</i> 
                            <div class="el-themes-img">
                                <div class="el-themes-animation">
                                    <img :src="item.c_cover" class="el-themes-scrollImg">
                                </div>
                            </div> 
                            <div class="el-themes-tool">
                                <div class="el-themes-name">
                                    <span class="title">{{item.title}}</span>
                                    <span class="price">
                                        <span :style="{color: item.price == 0.00 ? '#409EFF' : '#E6A23C'}">
                                            {{item.price == 0.00 ? '免费' : item.price + '元'}}
                                        </span>
                                    </span>
                                </div> 
                                <div class="el-themes-view">
                                    <el-button 
                                        icon="el-icon-search" 
                                        type="" 
                                        size="small" 
                                        @click="themeDetails(item)"
                                        round>
                                        详情
                                    </el-button>
                                    <el-button 
                                        v-if="item.install"
                                        :loading="item.installLoading"
                                        icon="el-icon-download"
                                        type="primary" 
                                        size="small" 
                                        @click="themeInstall(item)"
                                        round>
                                        安装
                                    </el-button>
                                    <el-button 
                                        v-if="item.shop"
                                        :loading="item.orderLoading"
                                        icon="el-icon-goods"
                                        type="warning" 
                                        size="small" 
                                        @click="themeCreateOrder(item)"
                                        round>
                                        购买
                                    </el-button>
                                    <el-button 
                                        v-if="item.uninstall"
                                        :loading="item.uninstallLoading"
                                        icon="el-icon-delete" 
                                        type="danger" 
                                        size="small" 
                                        @click="themeRemove(item)"
                                        round>
                                        卸载
                                    </el-button>
                                </div>
                            </div>
                        </div>
                    </div>
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
        <el-page-header @back="drawer=false" content="主题详情">
            <template v-slot:title>Esc键返回</template>
        </el-page-header>
        <div class="el-layout">
        <el-tabs tab-position="left" v-if="drawer">
            <el-tab-pane label="主题概览">
                <div class="el-pane-warp el-theme-single">
                    <div class="el-theme-info">
                        <p class="line">{{row.title}}</p>
                        <p class="line" v-if="orderId != 0">主题订单：{{orderId}}</p>
                        <p class="line">最近更新：{{row.update_time}}</p>
                        <p class="line">主题作者：<a :href="row.user.url" target="_blank">{{row.user.nickname}}</a></p>
                        <p class="line">安装次数：{{row.install_count}}</p>
                        <p class="line">主题价格：
                            <span :style="{color: row.price == 0.00 ? '#409EFF' : '#E6A23C'}">
                                {{row.price == 0.00 ? '免费' : row.price + '元'}}
                            </span>
                        </p>
                        <p class="line">主题分类：{{row.catalog}}</p>
                        <p class="line">主题标识：{{row.name}}</p>
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
                        <p v-if="row.relation_list.length > 0" class="line">关联插件：
                            <span class="plugin" v-for="(item, index) in row.relation_list" @click="pluginsChoose(item.title)">
                                {{item.title}}
                            </span>
                            <div style="color: #F56C6C">请注意，安装主题时会自动安装关联的插件 !</div>
                        </p>
                    </div>
                    <div class="el-theme-order" v-if="orderId != 0">
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
                    </div>
                </div>
            </el-tab-pane>
            <el-tab-pane label="主题预览">
                <div class="el-theme-preview">
                    <div class="header">
                        <i :class="urlMode == 0 ? 'el-icon-monitor' : 'el-icon-mobile'"></i>
                        <span>{{urlMode == 0 ? '电脑视图' : '手机视图'}}</span>
                        <span class="change" @click="urlMode = urlMode == 0 ? 1 : 0">点击切换视图</span>
                    </div>
                    <div class="warp" :class="urlMode == 0 ? 'pc' : 'wap'">
                        <iframe :src="row.preview_url"></iframe>
                    </div>
                </div>
            </el-tab-pane>
        </el-tabs>
    </div>
    </el-drawer>
</div>
<script>
    var themes = new Vue({
        el: '#app',
        data() {
            return {
                row: {},
                theme: theme,
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
                    install: '0'
                },
                total: 0,
                price:[{title: '全部', value: ''},{title: '免费', value: 'free'},{title: '付费', value: 'charge'}],
                install: <?php echo json_encode($install); ?>,
                catalog: <?php echo json_encode($catalog); ?>,
                indexUrl: 'themes/index',
                updateUrl: 'themes/update',
                installUrl: 'themes/install',
                pluginsInstallUrl: 'plugins/install',
                deleteUrl: 'themes/delete',
                urlMode: 0,
                orderId: 0,
                createOrderUrl: 'themes/createOrder',
                orderStatusUrl: 'themes/statusOrder',
                payMethod: 0,
                payQrcode: '',
                payStatus: '',
                payLoading: false,
                payMethodUrl: 'themes/payMethod',
                timer: null,
            }
        },
        created () {
            this.getData();
        },
        methods: {
            /**
             * 插件选择
             */
            pluginsChoose() {
                parent.parentVm.path = 'plugins/list';
            },
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
                        self.table   = res.data;
                        self.total   = res.count;
                        self.install = res.list;
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
            themeDetails(row) {
                this.row = row;
                this.drawer = true;
            },
            /**
             * 切换管理
             * @param  {Object} row 当前行
             */
            themeChange() {
                let self = this;
                self.$confirm('确定切换主题吗？', '', {
                    confirmButtonText: '确定',
                    cancelButtonText: '取消',
                    type: 'warning'
                }).then(() => {
                    request.post(self.updateUrl, {theme: self.theme}, function(res) {
                    if (res.status === 'success') {
                            self.getData(); 
                        } else {
                            self.$message({ showClose: true, message: res.message, type: res.status});
                        }
                    });
                }).catch(() => {});
            },
            /**
             * 主题购买
             * @param  {Object} row 当前行
             */
            themeCreateOrder(row) {
                let self = this;
                row.orderLoading = true;
                request.post(self.createOrderUrl, {name: row.name}, function(res) {
                    if (res.status === 'success') {
                        self.orderId = res.data.id;
                        self.themeDetails(row);
                        self.themeStatusOrder(row);
                        self.payMethodChange();
                    } else {
                        self.$message({ showClose: true, message: res.message, type: res.status});
                    }
                    row.orderLoading = false;
                });
            },
            /**
             * 主题订单状态
             * @param  {Object} row 当前行
             */
            themeStatusOrder(row) {
                let self = this;
                self.timer = setInterval(() => {
                    request.post(self.orderStatusUrl, {id: self.orderId}, function(res) {
                        if (res.status === 'success') {
                            self.payStatus = res.data;
                            if (self.payStatus === 2) {
                                self.themeInstall(row);
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
             * 主题安装
             * @param  {Object} row 当前行
             */
            themeInstall(row) {
                let self = this;
                row.installLoading = true;
                row.relation_list.forEach(function(item, index) {
                    self.pluginInstall(item);
                })
                request.post(self.installUrl, {name: row.name}, function(res) {
                    if (res.status === 'success') {
                        self.getData(); 
                    }
                    self.$message({ showClose: true, message: res.message, type: res.status});
                    row.installLoading = false;
                });
            },
            /**
             * 插件安装
             * @param  {Object} row 当前行
             */
            pluginInstall(row) {
                let self = this;
                request.post(self.pluginsInstallUrl, {name: row.name}, function(res) {
                    if (res.status != 'success') {
                        self.$message({ showClose: true, message: row.title + res.message, type: res.status});
                    }
                });
            },
            /**
             * 主题卸载
             * @param  {Object} row 当前行
             */
            themeRemove(row) {
                let self = this;
                self.$confirm('确定卸载'+row.title+'主题并删除数据吗？', '', {
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