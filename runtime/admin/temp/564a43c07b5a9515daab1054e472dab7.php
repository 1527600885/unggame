<?php /*a:3:{s:85:"/www/wwwroot/game.uswindltd.com/public/plugins/game/admin/view/mk_gamelist/index.html";i:1665307006;s:65:"/www/wwwroot/game.uswindltd.com/app/admin/view/common/header.html";i:1663904303;s:65:"/www/wwwroot/game.uswindltd.com/app/admin/view/common/footer.html";i:1650617578;}*/ ?>
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
						label: '编号',
						table: {
							sort: true,
						},
						form: false
					},
					{
						prop: 'displayStatus',
						label: '游戏状态',
						table: {
							is: 'el-switch',
							sort: true,
						},
						form: {
							is: 'el-switch',
							default: 0,
						}
					},
					{
						prop: 'gameType',
						label: '游戏类型',
						table: {
							sort: true,
						},
						form: {
							is: 'el-input',
						}
					},
					{
						prop: 'gameName',
						label: '游戏名称',
						form: false
						// form: {
						// 	is: 'el-input',
						// }
					},
					{
						prop: 'gameImage',
						label: '游戏图片',
						table: {
							is: 'image',
							width:'80px'
						},
						form: false
						// form: {
						// 	is: 'el-input',
						// }
					},
					{
						prop: 'tcgGameCode',
						label: '游戏代码',
						form: {
							is: 'el-input',
						}
					},
					{
						prop: 'productCode',
						label: '品牌简称',
						form: {
							is: 'el-input',
						}
					},
					{
						prop: 'productType',
						label: '产品代码',
						form: false
					},
					{
						prop: 'platform',
						label: '支持平台',
						form: false
					},
					{
						prop: 'gameSubType',
						label: '子目录',
						form: {
							is: 'el-input',
						}
					},
					{
						prop: 'hot',
						label: '热度',
						form: {
							is: 'el-input',
						}
					},
					{
						prop: 'is_groom',
						label: '是否推荐',
						table: {
							is: 'el-switch',
							sort: true,
						},
						form: {
							is: 'el-switch',
							default: 0,
						}
					},
					{
						prop: 'trialSupport',
						label: '试玩',
						table: {is: 'el-switch',sort: true},
						form: {
							is: 'el-switch',
							default: 1,
						}
					}
				],
			}
		},
	})
</script>
</body>
</html>