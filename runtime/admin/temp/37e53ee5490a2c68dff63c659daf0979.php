<?php /*a:3:{s:85:"/www/wwwroot/game.uswindltd.com/public/plugins/notice/admin/view/mk_notice/index.html";i:1665999399;s:65:"/www/wwwroot/game.uswindltd.com/app/admin/view/common/header.html";i:1663904303;s:65:"/www/wwwroot/game.uswindltd.com/app/admin/view/common/footer.html";i:1650617578;}*/ ?>
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
							sort: true,
						},
						form: false
					},
					// {
					// 	prop: 'title',
					// 	label: '标题',
					// 	table: {
					// 		sort: true,
					// 	},
					// 	form: {
					// 		is: 'el-input',
					// 	}
					// },
					{
						prop: 'en-us',
						label: '英语公告',
						table: {
						},
						form: {
							is: 'el-editor',
							placeholder: '',
						}
					},
					{
						prop: 'en-id',
						label: '印尼公告',
						table: {
						},
						form: {
							is: 'el-editor',
							placeholder: '',
						}
					},
					{
						prop: 'en-my',
						label: '马来公告',
						table: {
						},
						form: {
							is: 'el-editor',
							placeholder: '',
						}
					},
					{
						prop: 'ja-jp',
						label: '日本公告',
						table: {
						},
						form: {
							is: 'el-editor',
							placeholder: '',
						}
					},
					{
						prop: 'km-km',
						label: '柬埔寨公告',
						table: {
						},
						form: {
							is: 'el-editor',
							placeholder: '',
						}
					},
					{
						prop: 'ko-kr',
						label: '韩语公告',
						table: {
						},
						form: {
							is: 'el-editor',
							placeholder: '',
						}
					},
					{
						prop: 'th-th',
						label: '泰语公告',
						table: {
						},
						form: {
							is: 'el-editor',
							placeholder: '',
						}
					},
					{
						prop: 'vi-vn',
						label: '越南公告',
						table: {
						},
						form: {
							is: 'el-editor',
							placeholder: '',
						}
					},
					{
						prop: 'is_show',
						label: '是否显示',
						table: {
							sort: true,
						},
						form: {
							is: 'el-switch',
							default: '1',
						}
					},
				],
			}
		},
	})
</script>
</body>
</html>