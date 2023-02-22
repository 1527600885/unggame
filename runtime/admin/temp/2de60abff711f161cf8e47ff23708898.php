<?php /*a:3:{s:78:"D:/phpstudy_pro/WWW/uugame/public/plugins/order/admin/view/mk_order/index.html";i:1675751053;s:60:"D:\phpstudy_pro\WWW\uugame\app\admin\view\common\header.html";i:1675751052;s:60:"D:\phpstudy_pro\WWW\uugame\app\admin\view\common\footer.html";i:1675751052;}*/ ?>
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
		:table-page-sizes="[20, 50, 100, 200, 500]"
		:table-sort="{prop: 'id', order: 'desc'}"
		>
		
		<!-- <template v-slot:operation="row">
		   <el-tooltip content="审核订单" placement="top">
				<el-button type="primary" size="mini" plain @click="自定义">审核</el-button>
			</el-tooltip>
		</template> -->
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
							width:'80'
						},
						form: false
					},
					{
						prop: 'mer_order_no',
						label: '订单号',
						table: {
							sort: true,
						},
						form: {
							is: 'el-input',
						}
					},
					{
						prop: 'order_no',
						label: '第三方订单号',
						table: {
							sort: true,
						},
						form: {
							is: 'el-input',
						}
					},
					{
						prop: 'nickname',
						label: '充值用户',
						table: {
						},
						form: false
					},
					{
						prop: 'pid',
						label: '充值渠道',
						table: {
							prop: 'paymentname',
							sort: true,
						},
						form: {
						    is: 'el-select', 
						    child: {is: 'el-option',value: <?php echo json_encode($payment); ?>, props:{label: 'name', value: 'id'}},
						    rules: [
						        {required: true, message: '请选择充值渠道'},
						    ],
						    colMd: 6
						},
					},
					{
						prop: 'money',
						label: '充值金额',
						table: {
							sort: true,
						},
						form: {
							is: 'el-input',
						}
					},
					{
						prop: 'money2',
						label: '实际充值金额',
						table: {
							sort: true,
						},
						form: {
							is: 'el-input',
						}
					},
					{
						prop: 'type',
						label: '充值方式',
						table: {
							sort: true,
							label: '充值方式',
							prop:'types',
						},
						form: {
							is: 'el-input',
						}
					},
					{
						prop: 'status',
						label: '充值状态',
						table: {
							prop: 'statustext',
							is: 'el-switch',
							sort: true,
						},
						form: {
							is: 'el-input',
						}
					},
					{
						prop: 'time',
						label: '订单时间',
						table: {
							prop: 'ordertime',
							sort: true,
						},
						form: {
							is: 'el-input',
						}
					},
					{
						prop: 'time2',
						label: '到账时间',
						table: {
							prop: 'paytime',
							sort: true,
						},
						form: {
							is: 'el-input',
						}
					},
					{
						prop: 'imgurl',
						label: '充值凭证',
						table: {
							is: 'image',
							width:'80px'
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