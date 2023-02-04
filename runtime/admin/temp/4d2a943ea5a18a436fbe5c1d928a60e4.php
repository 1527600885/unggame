<?php /*a:3:{s:87:"/www/wwwroot/www.unicgm.com/public/plugins/share_set/admin/view/mk_share_set/index.html";i:1669177089;s:61:"/www/wwwroot/www.unicgm.com/app/admin/view/common/header.html";i:1663904303;s:61:"/www/wwwroot/www.unicgm.com/app/admin/view/common/footer.html";i:1650617578;}*/ ?>
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
					{
						prop: 'type',
						label: '类型',
						table: {
							sort: true,
						},
						form: false
					},
					{
						prop: 'language',
						label: '语言',
						table: {
							sort: true,
						},
						form: {
							is: 'el-select',
							placeholder: '',
							rules: [
								{required: true, message: '请输入'},
							],
							child: {
								value: [{"title":"美式英语","value":"en-us"},{"title":"印尼语","value":"en-id"},{"title":"马来语","value":"en-my"},{"title":"日语","value":"ja-jp"},{"title":"高棉语","value":"km-km"},{"title":"韩语","value":"ko-kr"},{"title":"泰语","value":"th-th"},{"title":"越南语","value":"vi-vn"}],
								props: {label: 'title', value: 'value'}},
						}
					},
					{
						prop: 'content',
						label: '分享得内容',
						table: {
							sort: true,
						},
						form: {
							is: 'el-editor',
							placeholder: '',
							rules: [
								{required: true, message: '请输入'},
							],
						}
					},
					{
						prop: 'add_time',
						label: '添加时间',
						table: {
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