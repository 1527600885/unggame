<?php /*a:3:{s:59:"/www/wwwroot/www.unicgm.com/app/admin/view/login/index.html";i:1672902372;s:61:"/www/wwwroot/www.unicgm.com/app/admin/view/common/header.html";i:1673339169;s:61:"/www/wwwroot/www.unicgm.com/app/admin/view/common/footer.html";i:1672902372;}*/ ?>
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
    <div class="el-login-wrapper">
        <div class="container">
            <div class="side">
                <div class="logo">
                    <img src="/admin/layui/logo.jpg">游戏管理平台
                </div>
                <div class="comments">请管理员登录本平台之后谨慎操作</div>
                <div class="footer">
                    <a href="<?php echo config('app.api'); ?>" target="_blank">©&nbsp;game.uswindltd.com&nbsp;</a>
                    <!-- <a href="<?php echo config('app.api'); ?>/blog.html" target="_blank">论坛专区</a>
                    <a href="<?php echo config('app.api'); ?>/docs.html" target="_blank">帮助文档</a>
                    <a href="<?php echo config('app.api'); ?>/onekey/userDeveloper/index" target="_blank">认证成为开发者</a> -->
                </div>
            </div>
            <div class="form">
                <div class="title">管理员登录</div>
                <div class="ctitle">ADMIN USER LOGIN</div>
                <el-form :model="loginForm" :rules="rules" ref="loginForm" @submit.native.prevent>
                    <template v-if="captchaShow">
                        <el-form-item v-if="captcha !== '' " prop="captcha">
                            <img :src="captcha" @click="getCaptcha()" />
                            <el-input v-model="loginForm.captcha" placeholder="请输入上方的图形验证码" @keyup.enter.native="submitForm()"></el-input>
                        </el-form-item>
                        <el-button @click="submitForm()"  :loading="loading" plain :disabled="loginForm.captcha.length === 4 ? false : true">验证</el-button>
                    </template>
                    <template v-else>
                        <el-form-item prop="loginAccount">
                            <el-input v-model="loginForm.loginAccount" prefix-icon="el-icon-user" placeholder="请输入账号/邮箱号" @keyup.enter.native="getCaptcha()">
                            </el-input>
                        </el-form-item>
                        <el-form-item prop="loginPassword">
                            <el-input v-model="loginForm.loginPassword" prefix-icon="el-icon-key" placeholder="请输入密码" show-password @keyup.enter.native="getCaptcha()">
                            </el-input>
                        </el-form-item>
                        <el-button @click="getCaptcha()" :loading="loading" plain>
                            登录
                        </el-button>
                        <div class="footer">
                            <a href="password">已有账号，忘记密码？</a>
                        </div>
                    </template>
                </el-form>
            </div>
        </div>
    </div>
</div>
<script>
    new Vue({
        el: '#app',
        data() {
            return {
                loading: false,
                captcha: "",
                captchaShow: false,
                loginForm: {
                    loginAccount: "",
                    loginPassword: "",
                    captcha: "",
                },
                rules: {
                    loginAccount: [
                        { required: true, message: '请输入账号/邮箱号', trigger: 'blur' },
                    ],
                    loginPassword: [
                        { required: true, message: '请输入密码', trigger: 'blur' },
                    ],  
                    captcha: [
                        { min: 4, message: '验证码长度有误', trigger: 'blur' }
                    ],  
                },
            }
        },
        methods: {
            /**
             * 准备登录
             */
            getCaptcha() {
                let self = this;
                request.post('login/isNeedVerification', {}, function(res){
                    let link  = admin_url('login/verify');
                    let param = link.indexOf('?') === -1 ? '?' : '&';
                    self.captcha = res.status === 'error' ? link + param + Math.random() : '';
                    self.$refs.loginForm.validate((valid) => {
                        if (valid) {
                            if (self.captcha !== '') {
                                self.captchaShow  = true;
                            } else {
                                self.submitForm();
                            }
                        } else {
                            return false;
                        }
                    });
                })
            },
            /**
             * 登录
             */
            submitForm() {
                let self = this;
                self.loading = true;
                request.post('login/index', self.loginForm, function (res){
                    self.loading = false;
                    if(res.status === 'success'){
						localStorage.setItem('admin_account',res.account)
                        top.location.href = admin_url();
                    } else {
                        self.captchaShow = false;
                        self.loginForm.captcha = "";
                        self.$notify({ showClose: true, message: res.message, type: res.status});
                    }
                })
            },
        },
    })
</script>
</body>
</html>