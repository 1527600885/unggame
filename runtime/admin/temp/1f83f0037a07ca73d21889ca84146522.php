<?php /*a:3:{s:87:"/www/wwwroot/game.uswindltd.com/public/plugins/payment/admin/view/mk_payment/index.html";i:1667011287;s:65:"/www/wwwroot/game.uswindltd.com/app/admin/view/common/header.html";i:1663904303;s:65:"/www/wwwroot/game.uswindltd.com/app/admin/view/common/footer.html";i:1650617578;}*/ ?>
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8">
<meta name="viewport" content="width=device-width,minimum-scale=1.0,maximum-scale=1.0,user-scalable=no">
<title>游戏后台管理系统</title>
<link rel="icon" href="/upload/favicon.ico"> 
<link rel="stylesheet" type="text/css" href="/admin/css/element.min.css?v=<?php echo config('app.version'); ?>">
<link rel="stylesheet" type="text/css" href="/admin/css/onekey.min.css?v=<?php echo config('app.version'); ?>">
<script type="text/javascript" src="/admin/js/jquery.min.js?v=<?php echo config('app.version'); ?>"></script>
<script type="text/javascript" src="/admin/js/vue.min.js?v=<?php echo config('app.version'); ?>"></script>
<script type="text/javascript" src="/admin/js/element.min.js?v=<?php echo config('app.version'); ?>"></script>
<script type="text/javascript" src="/admin/js/sortable.min.js?v=<?php echo config('app.version'); ?>"></script>
<script type="text/javascript" src="/admin/js/vuedraggable.min.js?v=<?php echo config('app.version'); ?>"></script>
<script type="text/javascript" src="/admin/js/common.js?v=<?php echo config('app.version'); ?>"></script>
<script type="text/javascript" src="/admin/js/component.js?v=<?php echo config('app.version'); ?>"></script>
<script type="text/javascript" src="/admin/js/nprogress.js?v=<?php echo config('app.version'); ?>"></script>
</head>
<body>
<div id="app" v-cloak>
	<el-curd
		:field="field"
		:table-export="false"
		:table-page-size="20"
		:table-page-sizes="[20, 50, 100, 200, 500]"
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
							width:'50px',
							sort: true,
						},
						form: false
					},
					{
						prop: 'name',
						label: '支付名称',
						table: {
							sort: true,
						},
						form: {
							is: 'el-input',
							placeholder: '请输入支付名称',
							rules: [
								{required: true, message: '请输入'},
							],
						}
					},
					{
						prop: 'type',
						label: '支付类型',
						table: {
							width:'100px',
							sort: true,
							prop: "type_text",
						},
						form: {
							is: 'el-radio-group',
							default: '1',
							rules: [
								{required: true},
							],
							child: {
								value: [{"title":"数字货币","value":"1"},{"title":"在线支付","value":"2"},{"title":"信用卡支付","value":"3"}],
								props: {label: 'title', value: 'value'}},
						}
					},
					{
						prop: 'logo',
						label: '支付logo',
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
						prop: 'image',
						label: '支付二维码',
						table: {
							is: 'image',
							width:'80px'
						},
						form: {
							is: 'el-file-select',
							tips: '数字货币必填',
							type: 'image',
							filterable: 1,
							multiple: 1,
						}
					},
					{
						prop: 'url',
						label: '支付地址',
						table: {
							is:'el-input'
						},
						form: {
							is: 'el-input',
							placeholder: '请输入支付地址',
							rules: [
								{required: true, message: '请输入'},
							],
						}
					},
					{
						prop: 'country',
						label: '支付国家',
						table: {
							prop: 'country_text',
							width:'100px',
							sort: true,
						},
						form: {
							is: 'el-select',
							placeholder: '',
							default: 'all',
							rules: [
								{required: true, message: '请输入'},
							],
							child: {
								value: [{"title":"美元","value":"en-us"},{"title":"印度尼西亚盾","value":"en-id"},{"title":"马来西亚令吉","value":"en-my"},{"title":"日元","value":"ja-jp"},{"title":"柬埔寨瑞尔","value":"km-km"},{"title":"韩元","value":"ko-kr"},{"title":"泰国铢","value":"th-th"},{"title":"越南盾","value":"vi-vn"},{"title":"全部","value":"all"}],
								props: {label: 'title', value: 'value'}},
						}
					},
					{
						prop: 'pay_memberid',
						label: '支付商户号',
						table: {
							label: '商户号',
							is:'el-input'
						},
						form: {
							is: 'el-input',
							placeholder: '请输入商户号',
							tips: '在线支付必填',
						}
					},
					{
						prop: 'md5key',
						label: '支付key',
						table: {
							is:'el-input'
						},
						form: {
							is: 'el-input',
							placeholder: '请输入在线支付key',
							tips: '在线支付必填',
						}
					},
					{
						prop: 'publickey',
						label: '支付公钥',
						table: {
							is:'el-input'
						},
						form: {
							is: 'el-input',
							placeholder: '请输入在线支付公钥',
							tips: '在线支付必填',
						}
					},
					{
						prop: 'privatekey',
						label: '支付私钥',
						table: {
							is:'el-input'
						},
						form: {
							is: 'el-input',
							placeholder: '请填写在线支付私钥',
							tips: '在线支付必填',
						}
					},
					{
						prop: 'channel',
						label: '支付通道',
						table: {
							is:'el-input'
						},
						form: {
							is: 'el-input',
							placeholder: '请填写在线支付通道',
							tips: '支付通道必填',
						}
					},
					{
						prop: 'currency_name',
						label: '货币名称',
						table: {
							is:'el-input'
						},
						form: {
							is: 'el-input',
							placeholder: '请填写在线货币名称',
							tips: '货币名称必填',
						}
					},
					{
						prop: 'busi_code',
						label: '支付类型编码',
						table: {
							is:'el-input'
						},
						form: {
							is: 'el-input',
							placeholder: '请填写在线支付类型编码',
							tips: '支付类型编码必填',
						}
					},
					{
						prop: 'is_show',
						label: '是否显示',
						table: {
							is: 'el-switch',
							sort: true,
						},
						form: {
							is: 'el-switch',
							default: 1,
						}
					},
				],
			}
		},
	})
</script>
</body>
</html>