<?php /*a:3:{s:98:"D:/phpstudy_pro/WWW/uugame/public/plugins/withdrawal_info/admin/view/mk_withdrawal_info/index.html";i:1675751053;s:60:"D:\phpstudy_pro\WWW\uugame\app\admin\view\common\header.html";i:1675751052;s:60:"D:\phpstudy_pro\WWW\uugame\app\admin\view\common\footer.html";i:1675751052;}*/ ?>
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
		:table-page-sizes="[20, 50, 100, 200, 500]"
		:search-status='[{"value":"1","label":"显示"},{"value":"0","label":"隐藏"}]'>
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
						label: '主键',
						table: {
							sort: true,
						},
						form: false
					},
					{
						prop: 'sid',
						label: '支付名称',
						table: {
							sort: true,
						},
						form: {
							is: 'el-input',
							disabled: true,
						}
					},
					{
						prop: 'uid',
						label: '用户昵称',
						table: {
						},
						form: {
							is: 'el-input',
							disabled: true,
						}
					},
					{
						prop: 'username',
						label: '账号名称',
						table: {
							sort: true,
						},
						form: {
							is: 'el-input',
						}
					},
					{
						prop: 'account',
						label: '账号',
						table: {
							sort: true,
						},
						form: {
							is: 'el-input',
						}
					},
					{
						prop: 'image',
						label: '图片',
						table: {
							sort: true,
						},
						form: {
							is: 'el-file-select',
							type: 'image',
							filterable: 1,
							multiple: 1,
						}
					},
					{
						prop: 'other1',
						label: '其他特殊的',
						table: {
							sort: true,
						},
						form: {
							is: 'el-input',
							placeholder: '填写其他特殊的需求',
							tips: '英文为主，用英文逗号隔开',
						}
					},
					{
						prop: 'is_show',
						label: '是否展示',
						table: {
							sort: true,
						},
						form: {
							is: 'el-switch',
							default: '1',
						}
					},
					{
						prop: 'add_time',
						label: '增加时间',
						table: {
							sort: true,
						},
						form: {
							is: 'el-input',
							disabled: true,
						}
					},
				],
			}
		},
	})
</script>
</body>
</html>