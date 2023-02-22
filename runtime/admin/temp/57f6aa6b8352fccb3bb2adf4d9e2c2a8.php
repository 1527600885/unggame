<?php /*a:3:{s:88:"D:/phpstudy_pro/WWW/uugame/public/plugins/customer/admin/view/mk_customer_log/index.html";i:1675751053;s:60:"D:\phpstudy_pro\WWW\uugame\app\admin\view\common\header.html";i:1675751052;s:60:"D:\phpstudy_pro\WWW\uugame\app\admin\view\common\footer.html";i:1675751052;}*/ ?>
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
						prop: 'to_id',
						label: '发送者',
						table: {
						},
						form: {
							is: 'el-input',
						}
					},
					{
						prop: 'form_id',
						label: '接收者',
						table: {
						},
						form: {
							is: 'el-input',
						}
					},
					{
						prop: 'room_id',
						label: '房间号码',
						table: {
							sort: true,
						},
						form: {
							is: 'el-input',
						}
					},
					{
						prop: 'type',
						label: '类型',
						table: false,
						form: false
					},
					{
						prop: 'content',
						label: '发送的内容',
						table: {
							sort: true,
						},
						form: {
							is: 'el-input',
						}
					},
					{
						prop: 'ip',
						label: '客户IP',
						table: {
							sort: true,
						},
						form: {
							is: 'el-input',
						}
					},
					{
						prop: 'online_time',
						label: '上线时间',
						table: {
							sort: true,
						},
						form: {
							is: 'el-input',
						}
					},
					{
						prop: 'out_time',
						label: '下线时间',
						table: {
							sort: true,
						},
						form: {
							is: 'el-input',
						}
					},
					{
						prop: 'add_time',
						label: '创建时间',
						table: {
							sort: true,
						},
						form: {
							is: 'el-input',
						}
					},
				],
			}
		},
	})
</script>
</body>
</html>