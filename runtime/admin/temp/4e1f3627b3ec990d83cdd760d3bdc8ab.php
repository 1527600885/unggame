<?php /*a:3:{s:98:"/www/wwwroot/www.unicgm.com/public/plugins/withdrawal/admin/view/mk_withdrawal_settings/index.html";i:1668040910;s:61:"/www/wwwroot/www.unicgm.com/app/admin/view/common/header.html";i:1670144435;s:61:"/www/wwwroot/www.unicgm.com/app/admin/view/common/footer.html";i:1670201344;}*/ ?>
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
		:search-catalog='[{"id":"1","title":"数字货币"},{"id":"2","title":"在线支付"}]'
		:search-status='[{"value":"1","label":"显示"},{"value":"0","label":"隐藏"}]'
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
						label: '编号',
						table: {
							sort: true,
							label: '编号',
						},
						form: false
					},
					{
						prop: 'name',
						label: '通道名称',
						table: {
							sort: true,
							label: '名称',
						},
						form: {
							is: 'el-input',
							placeholder: '通道名称',
							rules: [
								{required: true, message: '请输入通道名称'},
							],
						}
					},
					{
						prop: 'image',
						label: '通道图片',
						table: {
							is: 'image',
							width:'80px'
						},
						form: {
							is: 'el-file-select',
							type: 'image',
							filterable: 1,
							multiple: 1,
						}
					},
					{
						prop: 'type',
						label: '类型',
						table: {
							prop: 'typename',
						},
						form: {
							is: 'el-radio-group',
							default: '1',
							child: {
								value: [{"title":"数字货币","value":"1"},{"title":"在线支付","value":"2"}],
								props: {label: 'title', value: 'value'}},
						}
					},
					{
						prop: 'max',
						label: '提现次数',
						table: {
							sort: true,
						},
						form: {
							is: 'el-input-number',
							default: '0',
						}
					},
					{
						prop: 'is_show',
						label: '是否展示',
						table: {
							is: 'el-switch',
							sort: true,
						},
						form: {
							is: 'el-switch',
							default: 1,
						}
					},
					{
						prop: 'country',
						label: '支持国家',
						table: {
							sort: true,
						},
						form: {
							is: 'el-select',
							placeholder: '',
							default: 'all',
							child: {
								value: [{"title":"全部","value":"all"},{"title":"美元","value":"en-us"},{"title":"印度尼西亚盾","value":"en-id"},{"title":"马来西亚令吉","value":"en-my"},{"title":"日元","value":"ja-jp"},{"title":"柬埔寨瑞尔","value":"km-km"},{"title":"韩元","value":"ko-kr"},{"title":"泰国铢","value":"th-th"},{"title":"越南盾","value":"vi-vn"}],
								props: {label: 'title', value: 'value'}},
						}
					},
				],
			}
		},
	})
</script>
</body>
</html>