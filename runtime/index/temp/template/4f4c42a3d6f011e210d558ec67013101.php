<?php /*a:3:{s:69:"/www/wwwroot/www.unicgm.com/public/themes/template/user/register.html";i:1663640630;s:69:"/www/wwwroot/www.unicgm.com/public/themes/template/common/header.html";i:1663320458;s:66:"/www/wwwroot/www.unicgm.com/public/themes/template/common/app.html";i:1663640596;}*/ ?>

<!DOCTYPE html>
<html>
<head> 
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge" />
	<title><?php echo htmlentities($seo_title); ?></title>
	<meta name="keywords" content="<?php echo htmlentities($seo_keywords); ?>">
	<meta name="description" content="<?php echo htmlentities($seo_description); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
	<meta name="baidu-site-verification" content="code-09qLlhlH1O" />
	<link rel="shortcut icon" href="/upload/favicon.ico">
	<link rel="stylesheet" type="text/css" href="/themes/template/static/css/bootstrap.min.css"/>
	<link rel="stylesheet" type="text/css" href="/themes/template/static/css/onekey.min.css"/>
	<link rel="stylesheet" type="text/css" href="/themes/template/static/css/animates.css"/>
	<link rel="stylesheet" type="text/css" href="/themes/template/static/css/font-awesome.min.css"/>
	<link rel="stylesheet" type="text/css" href="/themes/template/static/css/jquery.mmenu.all.css"/>
	<link rel="stylesheet" type="text/css" href="/themes/template/static/css/swiper.min.css"/>
	<script type="text/javascript" src="/themes/template/static/js/jquery.min.js"></script>
	<script type="text/javascript" src="/themes/template/static/js/jquery.mmenu.all.js"></script>
	<script type="text/javascript" src="/themes/template/static/js/masonry.pkgd.min.js"></script>
	<script type="text/javascript" src="/themes/template/static/js/swiper.animate.min.js"></script>
	<script type="text/javascript" src="/themes/template/static/js/swiper.min.js"></script>
	<script type="text/javascript" src="/themes/template/static/js/wow.min.js"></script>
	<script type="text/javascript" src="/themes/template/static/js/common.js"></script>
</head>
<body>
<div id="page">
<header id="header">
	<div class="container">
		<div class="logo"><a href="/"><img src="<?php echo htmlentities($system['logo']); ?>" alt="" /></a></div>
		<div class="tel"><i class="fa fa-phone"></i> <span><?php echo htmlentities($system['telephone']); ?></span></div>
		<nav class="nav">
			<ul>
				<?php if(is_array($catalogHeader) || $catalogHeader instanceof \think\Collection || $catalogHeader instanceof \think\Paginator): $i = 0; $__LIST__ = $catalogHeader;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$item1): $mod = ($i % 2 );++$i;?>
				<li>
					<a href="<?php echo htmlentities($item1['url']); ?>"><?php echo htmlentities($item1['title']); ?></a>
					<ul>
						<?php if(is_array($item1['children']) || $item1['children'] instanceof \think\Collection || $item1['children'] instanceof \think\Paginator): $i = 0; $__LIST__ = $item1['children'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$item2): $mod = ($i % 2 );++$i;?>
						<li><a href="<?php echo htmlentities($item2['url']); ?>" ><?php echo htmlentities($item2['title']); ?></a></li>
						<?php endforeach; endif; else: echo "" ;endif; ?>
					</ul>
				</li>
				<?php endforeach; endif; else: echo "" ;endif; ?>
			</ul>
		</nav>
		<a href="#menu" class="mm_btn">
			<div class="menu_bar">
				<div class="menu_bar_item top">
					<div class="rect top"></div>
				</div>
				<div class="menu_bar_item mid">
					<div class="rect mid"></div>
				</div>
				<div class="menu_bar_item bottom">
					<div class="rect bottom"></div>
				</div>
			</div>
		</a> 
	</div>
</header>
<nav id="menu" class="mm-menu_offcanvas">
	<div id="panel-menu">
		<ul>
			<?php if(is_array($catalogHeader) || $catalogHeader instanceof \think\Collection || $catalogHeader instanceof \think\Paginator): $i = 0; $__LIST__ = $catalogHeader;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$item1): $mod = ($i % 2 );++$i;?>
			<li>
				<a href="<?php echo htmlentities($item1['url']); ?>"><?php echo htmlentities($item1['title']); ?></a>
				<ul>
					<?php if(is_array($item1['children']) || $item1['children'] instanceof \think\Collection || $item1['children'] instanceof \think\Paginator): $i = 0; $__LIST__ = $item1['children'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$item2): $mod = ($i % 2 );++$i;?>
					<li><a href="<?php echo htmlentities($item2['url']); ?>" ><?php echo htmlentities($item2['title']); ?></a></li>
					<?php endforeach; endif; else: echo "" ;endif; ?>
				</ul>
			</li>
			<?php endforeach; endif; else: echo "" ;endif; ?>
		</ul>
	</div>
</nav>	
<link rel="stylesheet" type="text/css" href="/themes/template/static/css/element.min.css">
<script type="text/javascript" src="/themes/template/static/js/vue.min.js"></script>
<script type="text/javascript" src="/themes/template/static/js/element.min.js"></script>
<div id="app" v-cloak>
    <div class="el-warp">
        <div class="el-login">
            <div class="describe">
                <img src="<?php echo htmlentities($system['logo']); ?>">
                <div class="content">基于Thinkphp6+Element的通用后台开发框架。一键安装插件/一键安装模板/一键生成代码/一键生成菜单权限/一键生成API接口， 网站、小程序、APP、ERP一个后台框架统统搞定！</div>
                <div class="footer">onekeyadmin 企业版 - 企业级系统开发平台</div>
            </div>
            <div class="operation">
                <div class="title">用户注册</div>
                <div class="ctitle">USER REGISTER</div>
                <el-form :model="ruleForm" :rules="rules" ref="ruleForm" @submit.native.prevent>
                    <el-form-item prop="email">
                        <el-input 
                            v-model="ruleForm.email" 
                            prefix-icon="el-icon-user" 
                            placeholder="请输入邮箱" 
                            @input="showGetCode()">
                        </el-input>
                    </el-form-item>
                    <el-form-item prop="code" v-if="regular">
                        <el-input 
                            style="width: 200px" 
                            v-model="ruleForm.code" 
                            prefix-icon="el-icon-time" 
                            placeholder="请输入验证码">
                        </el-input>
                        <template v-if="codeLoading">
                            <el-button disabled>正在获取中...</el-button>
                        </template>
                        <template v-else>
                            <el-button @click="getCode()" :disabled="nowInterval != 0">
                                {{nowInterval == 0 ? '点击获取验证码': nowInterval + '秒重新后获取'}}
                            </el-button>
                        </template>
                    </el-form-item>
                    <el-form-item prop="password">
                        <el-input 
                            v-model="ruleForm.password" 
                            prefix-icon="el-icon-key" 
                            placeholder="请填写密码" 
                            show-password 
                            @keyup.enter.native="register()">
                        </el-input>
                    </el-form-item>
                    <el-button 
                        class="go" 
                        type="primary"
                        :loading="loading"
                        @click="register()">
                        立即注册
                    </el-button>
                    <div class="footer">
                        <a href="<?php echo index_url('login/index'); ?>">去登录</a>
                        <a href="<?php echo index_url('login/password'); ?>">忘记密码？</a>
                    </div>
                </el-form>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    new Vue({
        el: '#app',
        data() {
            return {
                loading: false,
                codeLoading: false,
                regular: false,
                nowInterval: 0,
                timer: '',
                ruleForm: {
                    uid: locationUrl.get('uid'),
                    email: "",
                    password: "",
                    code: "",
                    salt: "",
                    rcode: "",
                },
                rules: {
                    email: [
                        { required: true, message: '请输入邮箱号', trigger: 'blur' },
                        { pattern: /^([a-zA-Z0-9]+[_|\_|\.]?)*[a-zA-Z0-9]+@([a-zA-Z0-9]+[_|\_|\.]?)*[a-zA-Z0-9]+\.[a-zA-Z]{2,3}$/, message: '邮箱号输入格式不对', trigger: 'blur' },
                    ],
                    password: [
                        { required: true, message: '请输入密码', trigger: 'blur' },
                    ],
                    code: [
                        { required: true, message: '请输入验证码', trigger: 'blur' },
                    ],
                },
            }
        },
        methods: {
            /**
             * 显示获取邮箱验证码
             */
            showGetCode() {
                let email  = /^([a-zA-Z0-9]+[_|\_|\.]?)*[a-zA-Z0-9]+@([a-zA-Z0-9]+[_|\_|\.]?)*[a-zA-Z0-9]+\.[a-zA-Z]{2,3}$/;
                this.regular = email.test(this.ruleForm.email);
            },
            /**
             * 获取邮箱验证码
             */
            getCode() {
                let self = this;
                if (self.nowInterval == 0) {
                    self.codeLoading = true;
                    post('api/login/sendRegisterEmailCode', {email: self.ruleForm.email}, function(res){
                        self.codeLoading = false;
                        self.$message({ showClose: true, message: res.message, type: res.status});
                        if (res.status === 'success') {
                            self.nowInterval = 60;
                            self.timer        = setInterval(() => {
                                if (self.nowInterval > 0) {
                                    self.nowInterval--;
                                }
                            }, 1000);
                            self.ruleForm.salt  = res.salt;
                            self.ruleForm.rcode = res.code;
                        }
                    })
                }
            },
            /**
             * 点击立即注册
             */
            register() {
                let self = this;
                self.$refs.ruleForm.validate((valid) => {
                    if (valid) {
                        self.loading = true;
                        post('api/login/registerEmail', self.ruleForm, function(res){
                            self.loading = false;
                            if (res.status == 'success') {
                                self.$message({ showClose: true, message: res.message, type: res.status, onClose:function(){
                                    location.href = "<?php echo index_url('login/index'); ?>"
                                }});
                            } else {
                                self.$message({ showClose: true, message: res.message, type: res.status});
                            }
                        });
                    } else {
                        return false;
                    }
                });
            },
        },
        beforeDestroy() {
            clearInterval(this.timer);
        },
    });
</script>
</body>
</html>