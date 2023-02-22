<?php /*a:3:{s:87:"D:/phpstudy_pro/WWW/uugame/public/plugins/ungcoin/admin/view/mk_ungcoin_flow/index.html";i:1675951187;s:60:"D:\phpstudy_pro\WWW\uugame\app\admin\view\common\header.html";i:1675751052;s:60:"D:\phpstudy_pro\WWW\uugame\app\admin\view\common\footer.html";i:1675751052;}*/ ?>
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
						label: '主键',
						table: {
							sort: true,
						},
						form: false
					},
					{
						prop: 'userid',
						label: '转账方用户ID',
						table: {
							sort: true,
						},
						form: {
							is: 'el-input',
						}
					},
					{
						prop: 'touserid',
						label: '接收方用户ID',
						table: {
							sort: true,
						},
						form: {
							is: 'el-input',
						}
					},
					{
						prop: 'amount',
						label: '货币数量',
						table: {
							sort: true,
						},
						form: {
							is: 'el-input',
						}
					},
					{
						prop: 'type',
						label: '转账类型:1:用户之间转账，2:后台转账，3:用户申购，4：用户赎回',
						table: {
							sort: true,
						},
						form: {
							is: 'el-input',
						}
					},
					{
						prop: 'create_time',
						label: '转账时间',
						table: {
							sort: true,
						},
						form: {
							is: 'el-input',
						}
					},
					{
						prop: 'orderno',
						label: '订单编号',
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