<?php /*a:3:{s:81:"D:/phpstudy_pro/WWW/uugame/public/plugins/game/admin/view/mk_gamebrand/index.html";i:1675751053;s:60:"D:\phpstudy_pro\WWW\uugame\app\admin\view\common\header.html";i:1675751052;s:60:"D:\phpstudy_pro\WWW\uugame\app\admin\view\common\footer.html";i:1675751052;}*/ ?>
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
		:table-sort="{prop: 'id', order: 'desc'}"
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
						label: '品牌名称',
						table: {
							sort: true,
						},
						form: {
							is: 'el-input',
						}
					},
					{
						prop: 'code',
						label: '品牌代码',
						table: {
							sort: true,
						},
						form: {
							is: 'el-input',
						}
					},
					{
						prop: 'logo',
						label: 'LOGO',
						table: {
							is: 'image',
							width:'80px'
						},
						form: {
							is: 'el-file-select',type: 'image'
						}
					},
					{
						prop: 'gametype',
						label: '包含游戏类型',
						table: {
							sort: true,
						},
						form: {
							is: 'el-input',
						}
					},
					{
						prop: 'RNG_totalCount',
						label: '电子游戏总数',
						table: {
							sort: true,
						},
						form: {
							is: 'el-input-number',
							default: '0',
						}
					},
					{
						prop: 'LOTT_totalCount',
						label: '彩票游戏总数',
						table: {
							sort: true,
						},
						form: {
							is: 'el-input-number',
							default: '0',
						}
					},
					{
						prop: 'LIVE_totalCount',
						label: '真人游戏总数',
						table: {
							sort: true,
						},
						form: {
							is: 'el-input-number',
							default: '0',
						}
					},
					{
						prop: 'FISH_totalCount',
						label: '捕鱼游戏总数',
						table: {
							sort: true,
						},
						form: {
							is: 'el-input-number',
							default: '0',
						}
					},
					{
						prop: 'PVP_totalCount',
						label: '棋牌游戏总数',
						table: {
							sort: true,
						},
						form: {
							is: 'el-input-number',
							default: '0',
						}
					},
					{
						prop: 'SPORT_totalCount',
						label: '体育游戏总数',
						table: {
							sort: true,
						},
						form: {
							is: 'el-input-number',
							default: '0',
						}
					},
					{
						prop: 'ESPORT_totalCount',
						label: '电竞游戏总数',
						table: {
							sort: true,
						},
						form: {
							is: 'el-input-number',
							default: '0',
						}
					},
					{
						prop: 'status',
						label: '品牌状态',
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