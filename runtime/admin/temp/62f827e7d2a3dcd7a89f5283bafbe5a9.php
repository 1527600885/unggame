<?php /*a:3:{s:79:"/www/wwwroot/www.unicgm.com/public/plugins/ung/admin/view/mk_ung_set/index.html";i:1670985367;s:61:"/www/wwwroot/www.unicgm.com/app/admin/view/common/header.html";i:1670144435;s:61:"/www/wwwroot/www.unicgm.com/app/admin/view/common/footer.html";i:1670201344;}*/ ?>
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
						prop: 'name',
						label: '名称',
						table: {
							sort: true,
						},
						form: {
							is: 'el-input',
						}
					},
					{
						prop: 'currency_num',
						label: '数量',
						table: {
							sort: true,
							label: '货币数量',
						},
						form: {
							is: 'el-input',
						}
					},
					{
						prop: 'interest',
						label: '利率',
						table: {
							sort: true,
						},
						form: {
							is: 'el-input',
						}
					},
					{
						prop: 'explain',
						label: '说明',
						table: false,
						form: false
					},
					{
						prop: 'title',
						label: '介绍头部',
						table: {
							sort: true,
						},
						form: {
							is: 'el-input',
						}
					},
					{
						prop: 'content',
						label: '介绍内容',
						table: {
							sort: true,
						},
						form: {
							is: 'el-editor',
							placeholder: '',
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
					{
						prop: 'price',
						label: '单个购买的价格',
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