<?php /*a:3:{s:60:"D:\phpstudy_pro\WWW\uugame\app\admin\view\console\index.html";i:1675751052;s:60:"D:\phpstudy_pro\WWW\uugame\app\admin\view\common\header.html";i:1675751052;s:60:"D:\phpstudy_pro\WWW\uugame\app\admin\view\common\footer.html";i:1675751052;}*/ ?>
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
<?php if(empty($html) || (($html instanceof \think\Collection || $html instanceof \think\Paginator ) && $html->isEmpty())): ?>
<div id="app" v-cloak>
    <div class="el-console">
        <el-row :gutter="20">
            <el-col :sm="16">
                <el-row :gutter="20">
                    <el-col :sm="12" :xs="24">
            			<div class="web" @click="parent.parentVm.clickMenu('plugins/list')">
            				<div class="title"><i class="el-icon-monitor"></i>控制台</div>
            				<div class="desc">亲亲，可以安装流量统计插件填充内容~</div>
            				<div class="btn">
            				    <el-button size="small" plain>去安装</el-button>
            				</div>
            			</div>
        			</el-col>
        			<el-col :sm="12" :xs="24">
                		<a class="domain" href="<?php echo htmlentities($domain); ?>" target="_blank">
                			<div class="title">我的网站</div>
                			<div class="bind"><?php echo htmlentities($domain); ?></div>
                			<el-button size="small" plain>去看看</el-button>
                		</a>
            		</el-col>
            	</el-row>
        	</el-col>
    	</el-row>
	</div>
</div>
<script>
new Vue({
    el: '#app',
    data() {
        return {}
    },
})
</script>
<?php else: ?>
<?php echo $html; ?>
<?php endif; ?>
</body>
</html>