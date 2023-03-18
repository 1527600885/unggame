<?php /*a:3:{s:54:"E:\project\unggame\app\admin\view\numberone\index.html";i:1678072223;s:52:"E:\project\unggame\app\admin\view\common\header.html";i:1673339169;s:52:"E:\project\unggame\app\admin\view\common\footer.html";i:1672902372;}*/ ?>
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8">
<meta name="viewport" content="width=device-width,minimum-scale=1.0,maximum-scale=1.0,user-scalable=no">
<title>游戏后台管理系统</title>
<link rel="icon" href="/upload/favicon.ico"> 
<link rel="stylesheet" type="text/css" href="/admin/css/element.min.css?v=<?php echo config('app.version'); ?>">
<link rel="stylesheet" type="text/css" href="/admin/css/onekey.min.css?v=<?php echo config('app.version'); ?>">
<link rel="stylesheet" type="text/css" href="/admin/layui/css/layui.css?v=<?php echo config('app.version'); ?>">
<script type="text/javascript" src="/admin/js/jquery.min.js?v=<?php echo config('app.version'); ?>"></script>
<script type="text/javascript" src="/admin/js/vue.min.js?v=<?php echo config('app.version'); ?>"></script>
<script type="text/javascript" src="/admin/js/element.min.js?v=<?php echo config('app.version'); ?>"></script>
<script type="text/javascript" src="/admin/js/sortable.min.js?v=<?php echo config('app.version'); ?>"></script>
<script type="text/javascript" src="/admin/js/vuedraggable.min.js?v=<?php echo config('app.version'); ?>"></script>
<script type="text/javascript" src="/admin/js/common.js?v=<?php echo config('app.version'); ?>"></script>
<script type="text/javascript" src="/admin/js/component.js?v=<?php echo config('app.version'); ?>"></script>
<script type="text/javascript" src="/admin/js/nprogress.js?v=<?php echo config('app.version'); ?>"></script>
<script type="text/javascript" src="/admin/layui/layui.js?v=<?php echo config('app.version'); ?>"></script>
</head>
<body>
<div id="app" v-cloak>
    <div class="el-layout">
        <el-tabs v-model="tabs" :tab-position="document.body.clientWidth > 768 ? 'left' : 'top'">
            <el-tab-pane label="第一个游戏配置" name="numberoneuser">
                <div class="el-pane-warp">
                    <el-form ref="numberoneuserForm" :model="numberoneuserForm" label-width="120px">
<!--                        <el-form-item>-->
<!--                            <template slot="label">-->
<!--                                <div><el-tooltip placement="top" content="{$numberoneuser.gameImage}"><div>游戏图片：</div></el-tooltip></div>-->
<!--                            </template>-->
<!--                            <el-file-select v-model="numberoneuserForm.gameImage"></el-file-select>-->
<!--                        </el-form-item>-->
                        <el-form-item label="用户名称">
                            <el-input v-model="numberoneuserForm.username" 	style="width: 40%"></el-input>
                        </el-form-item>
                        <el-form-item label="赢得金额">
                            <el-input v-model="numberoneuserForm.price"  type="number"	style="width: 40%"></el-input>
                        </el-form-item>
                        <el-form-item label="游戏">
                            <el-autocomplete
                                    v-model="numberoneuserForm.gamename"
                                    :fetch-suggestions="querySearchAsync"
                                    placeholder="请输入内容"
                                    @select="handleSelect"
                            ></el-autocomplete>
                        </el-form-item>
                        <el-form-item label="游戏图片">
                            <el-image v-model="numberoneuserForm.gameImage" :src="numberoneuserForm.gameImage"></el-image>
                        </el-form-item>
                        <div class="el-bottom">
                            <el-button
                                    size="medium"
                                    :loading="loading"
                                    type="primary"
                                    icon="el-icon-refresh-right"
                                    @click="saveConfig('第一名用户配置', numberoneuserForm)">
                                保 存
                            </el-button>
                        </div>
                    </el-form>
                </div>
            </el-tab-pane>
            <el-tab-pane label="第二个游戏配置" name="numbertwouser">
                <div class="el-pane-warp">
                    <el-form ref="numbertwouserForm" :model="numbertwouserForm" label-width="120px">
                        <el-form-item label="用户名称">
                            <el-input v-model="numbertwouserForm.username" 	style="width: 40%"></el-input>
                        </el-form-item>
                        <el-form-item label="赢得金额">
                            <el-input v-model="numbertwouserForm.price"  type="number"	style="width: 40%"></el-input>
                        </el-form-item>
                        <el-form-item label="游戏">
                            <el-autocomplete
                                    v-model="numbertwouserForm.gamename"
                                    :fetch-suggestions="querySearchAsync"
                                    placeholder="请输入内容"
                                    @select="handleSelect2"
                            ></el-autocomplete>
                        </el-form-item>
                        <el-form-item label="游戏图片">
                            <el-image v-model="numbertwouserForm.gameImage" :src="numbertwouserForm.gameImage"></el-image>
                        </el-form-item>
                        <div class="el-bottom">
                            <el-button
                                    size="medium"
                                    :loading="loading"
                                    type="primary"
                                    icon="el-icon-refresh-right"
                                    @click="saveConfig('第二名用户配置', numbertwouserForm)">
                                保 存
                            </el-button>
                        </div>
                    </el-form>
                </div>
            </el-tab-pane>
            <el-tab-pane label="第三个游戏配置" name="numberthreeuser">
                <div class="el-pane-warp">
                    <el-form ref="numberthreeuserForm" :model="numberthreeuserForm" label-width="120px">
                        <el-form-item label="用户名称">
                            <el-input v-model="numberthreeuserForm.username" 	style="width: 40%"></el-input>
                        </el-form-item>
                        <el-form-item label="赢得金额">
                            <el-input v-model="numberthreeuserForm.price"  type="number"	style="width: 40%"></el-input>
                        </el-form-item>
                        <el-form-item label="游戏">
                            <el-autocomplete
                                    v-model="numberthreeuserForm.gamename"
                                    :fetch-suggestions="querySearchAsync"
                                    placeholder="请输入内容"
                                    @select="handleSelect3"
                            ></el-autocomplete>
                        </el-form-item>
                        <el-form-item label="游戏图片">
                            <el-image v-model="numberthreeuserForm.gameImage" :src="numberthreeuserForm.gameImage"></el-image>
                        </el-form-item>
                        <div class="el-bottom">
                            <el-button
                                    size="medium"
                                    :loading="loading"
                                    type="primary"
                                    icon="el-icon-refresh-right"
                                    @click="saveConfig('第三名用户配置', numberthreeuserForm)">
                                保 存
                            </el-button>
                        </div>
                    </el-form>
                </div>
            </el-tab-pane>
            <el-tab-pane label="第四个游戏配置" name="numberfouruser">
                <div class="el-pane-warp">
                    <el-form ref="numberfouruserForm" :model="numberfouruserForm" label-width="120px">
                        <el-form-item label="用户名称">
                            <el-input v-model="numberfouruserForm.username" 	style="width: 40%"></el-input>
                        </el-form-item>
                        <el-form-item label="赢得金额">
                            <el-input v-model="numberfouruserForm.price"  type="number"	style="width: 40%"></el-input>
                        </el-form-item>
                        <el-form-item label="游戏">
                            <el-autocomplete
                                    v-model="numberfouruserForm.gamename"
                                    :fetch-suggestions="querySearchAsync"
                                    placeholder="请输入内容"
                                    @select="handleSelect4"
                            ></el-autocomplete>
                        </el-form-item>
                        <el-form-item label="游戏图片">
                            <el-image v-model="numberfouruserForm.gameImage" :src="numberfouruserForm.gameImage"></el-image>
                        </el-form-item>
                        <div class="el-bottom">
                            <el-button
                                    size="medium"
                                    :loading="loading"
                                    type="primary"
                                    icon="el-icon-refresh-right"
                                    @click="saveConfig('第四名用户配置', numberfouruserForm)">
                                保 存
                            </el-button>
                        </div>
                    </el-form>
                </div>
            </el-tab-pane>
        </el-tabs>
    </div>
</div>
<script>
    new Vue({
        el: '#app',
        data() {
            return {
                tabs: 'numberoneuser',
                editUrl: "config/update",
                loading: false,
                options :[],
                restaurants:[],
                numberoneuserForm:<?php echo json_encode($numberoneuser); ?>,
                numbertwouserForm:<?php echo json_encode($numbertwouser); ?>,
                numberthreeuserForm:<?php echo json_encode($numberthreeuser); ?>,
                numberfouruserForm:<?php echo json_encode($numberfouruser); ?>
            }
        },

        created() {

        },
        methods: {
            /**
             * 保存基础配置
             */
            saveConfig(title, value) {
                let self = this;
                let row = {
                    gameImage : value.gameImage,
                    gamename : value.gamename,
                    username : value.username,
                    price : value.price,
                }
                self.loading = true;
                request.post(self.editUrl, {title: title, name: self.tabs, value: row}, function(res){
                    self.loading = false;
                    self.$notify({showClose: true, message: res.message, type: res.status});
                });
            },
            querySearchAsync(queryString, cb) {
                request.post("numberone/getGameList",{key:queryString},function (res){
                    var results = res.data;
                    cb(results);
                })
            },
            handleSelect4(item){
                this.numberfouruserForm.gameImage = item.gameImage
            },
            handleSelect3(item){
                this.numberthreeuserForm.gameImage = item.gameImage
            },
            handleSelect2(item){
                this.numbertwouserForm.gameImage = item.gameImage
            },
            handleSelect(item){
                this.numberoneuserForm.gameImage = item.gameImage
            }
        },
    })
</script>
</body>
</html>