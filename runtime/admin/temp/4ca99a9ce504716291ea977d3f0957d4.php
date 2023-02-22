<?php /*a:3:{s:88:"D:/phpstudy_pro/WWW/uugame/public/plugins/withdrawal/admin/view/mk_withdrawal/index.html";i:1675751053;s:60:"D:\phpstudy_pro\WWW\uugame\app\admin\view\common\header.html";i:1675751052;s:60:"D:\phpstudy_pro\WWW\uugame\app\admin\view\common\footer.html";i:1675751052;}*/ ?>
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
		:search-status='[{"value":"0","label":"未审核"},{"value":"1","label":"已审核"}]'
		:search-date="false">
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
						label: '配置ID',
						table: false,
						form: false
					},
					{
						prop: 'uid',
						label: '用户ID',
						table: {
							
						},
						form: false
					},
					{
						prop: 'type',
						label: '提现类型',
						table: {
							prop:"type_name",
							sort: true,
						},
						form: {
							is: 'el-radio-group',
							default: '1',
							child: {
								value: [{"title":"数字货币提现","value":"1"},{"title":"在线提现","value":"2"}],
								props: {label: 'title', value: 'value'}},
						}
					},
					{
						prop: 'currency',
						label: '货币类型',
						table: {
							sort: true,
						},
						form: false
					},
					{
						prop: 'name',
						label: '姓名',
						table: {
							sort: true,
						},
						form: {
							is: 'el-input',
						}
					},
					{
						prop: 'address',
						label: '地址',
						table: {
							sort: true,
						},
						form: {
							is: 'el-input',
						}
					},
					{
						prop: 'other',
						label: '其他信息',
						table: {
							prop: 'other_name',
							sort: true,
						},
						form: {
							is: 'el-input',
						}
					},
					{
						prop: 'amount',
						label: '提现金额',
						table: {
							prop: 'amount_name',
							sort: true,
						},
						form: {
							is: 'el-input',
						}
					},
					{
						prop: 'money',
						label: '实际到账金额',
						table: {
							prop: 'money_name',
							sort: true,
						},
						form: {
							is: 'el-input',
						}
					},
					{
						prop: 'charge',
						label: '手续费',
						table: {
							prop: 'charge_name',
							sort: true,
						},
						form: {
							is: 'el-input',
						}
					},
					{
						prop: 'online_status',
						label: '转账状态',
						table: {
							prop: 'online_status_name',
							sort: true,
						},
						form: {
							is: 'el-switch',
							default: '1',
						}
					},
					{
						prop: 'status',
						label: '提现状态',
						table: {
							sort: true,
							is: 'el-switch',
						},
						form: {
							is: 'el-switch',
							default: '1',
						}
					},
					{
						prop: 'status_time',
						label: '审核时间',
						table: {
							sort: true,
						},
						form: false
					},
					{
						prop: 'pay_time',
						label: '到账时间',
						table: {
							sort: true,
						},
						form: false
					},
					{
						prop: 'add_time',
						label: '提现时间',
						table: {
							prop: 'add_times',
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