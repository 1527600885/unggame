<?php /*a:3:{s:79:"D:/phpstudy_pro/WWW/uugame/public/plugins/ung/admin/view/mk_ung_user/index.html";i:1676095655;s:60:"D:\phpstudy_pro\WWW\uugame\app\admin\view\common\header.html";i:1675751052;s:60:"D:\phpstudy_pro\WWW\uugame\app\admin\view\common\footer.html";i:1675751052;}*/ ?>
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
	<el-curd
		:field="field"
		:table-export="false"
		:table-page-size="20"
		:table-page-sizes="[20, 50, 100, 200, 500]">
	</el-curd>
</div>
<script>
	new Vue({
		el: "#app",
		data() {
			return {
				field: [
					{
						prop: 'id',
						label: 'id',
						table: {
							sort: true,
						},
						form: false
					},
					{
						prop: 'nickname',
						label: '用户昵称',
						table: {
							sort: true,
							label: '用户',
						},
						form: false
					},
					{
						prop: 'ung_id',
						label: '虚拟货币ID',
						table: {
							sort: true,
							label: '虚拟币名称',
						},
						form: false
					},
					{
						prop: 'num',
						label: '拥有的数量',
						table: {
							sort: true,
						},
						form: {
							is: 'el-input',
						}
					},
					{
						prop: 'money',
						label: '拥有的资产',
						table: {
							sort: true,
						},
						form: {
							is: 'el-input',
						}
					},
					{
						prop: 'update_time',
						label: '修改时间',
						table: {
							sort: true,
						},
						form: false
					},
					{
						prop: 'add_time',
						label: '创建时间',
						table: {
							sort: true,
						},
						form: false
					},
				],
			}
		},
	})
</script>
</body>
</html>