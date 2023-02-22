<?php /*a:3:{s:80:"D:/phpstudy_pro/WWW/uugame/public/plugins/game/admin/view/mk_gamelist/index.html";i:1675751053;s:60:"D:\phpstudy_pro\WWW\uugame\app\admin\view\common\header.html";i:1675751052;s:60:"D:\phpstudy_pro\WWW\uugame\app\admin\view\common\footer.html";i:1675751052;}*/ ?>
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
		ref="curd"
		@get-data="getData($event)"
		:table-page-size="20"
		:table-page-sizes="[20, 50, 100, 200, 500]"
		:search-date="false">
		<template v-slot:search>
		   <el-select v-if="brandlist" v-model="brandvalue" clearable placeholder="请选择游戏品牌" size="small" @change="getgamecode">
		       <el-option
		         v-for="item in brandlist"
		         :key="item.value"
		         :label="item.label"
		         :value="item.value">
		       </el-option>
		     </el-select>
			 <el-select v-model="value" placeholder="请选择游戏类型" size="small" @change="getgametype">
			     <el-option
			       v-for="item in options"
			       :key="item.value"
			       :label="item.label"
			       :value="item.value">
			     </el-option>
			   </el-select>
		</template>
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
					// {
					// 	prop: 'tcgGameCode',
					// 	label: '游戏代码',
					// 	form: {
					// 		is: 'el-input',
					// 	}
					// },
					{
						prop: 'productCode',
						label: '品牌简称',
						form: {
							is: 'el-input',
						}
					},
					// {
					// 	prop: 'productType',
					// 	label: '产品代码',
					// 	form: false
					// },
					// {
					// 	prop: 'platform',
					// 	label: '支持平台',
					// 	form: false
					// },
					// {
					// 	prop: 'gameSubType',
					// 	label: '子目录',
					// 	form: {
					// 		is: 'el-input',
					// 	}
					// },
					{
						prop: 'is_visit',
						label: '是否可访问',
						table: {
							prop: 'visit',
							sort: true,
						},
						form: false
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
						label: '游戏推荐',
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
						prop: 'groom_sort',
						label: '推荐排位',
						table: {
							is: 'el-input',
							sort: true,
						},
						form: {
							is: 'el-input',
							default: 0,
						}
					},
					{
						prop: 'category_put',
						label: '游戏类型',
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
						prop: 'category_sort',
						label: '类型排位',
						table: {
							is: 'el-input',
							sort: true,
						},
						form: {
							is: 'el-input',
							default: 0,
						}
					},
					// {
					// 	prop: 'trialSupport',
					// 	label: '试玩',
					// 	table: {is: 'el-switch',sort: true},
					// 	form: {
					// 		is: 'el-switch',
					// 		default: 1,
					// 	}
					// }
				],
				brandlist:null,
				brandvalue:'',
				options: [{
				  value: 'RNG',
				  label: '电子'
				}, {
				  value: 'FISH',
				  label: '捕鱼'
				}, {
				  value: 'PVP',
				  label: '棋牌'
				}, {
				  value: 'SPORT',
				  label: '体育'
				}, {
				  value: 'ESPORT',
				  label: '电竞'
				}, {
				  value: 'LIVE',
				  label: '真人'
				}, {
				  value: 'LOTT',
				  label: '彩票'
				}],
				value: '',
			}
			
		},
		methods:{
			getData(res) {
				this.brandlist=res.branlist;
				// this.brandlist='nihao';
			},
			getgametype(e){
				this.$refs.curd.search = Object.assign({}, this.$refs.curd.search, {page: 1, gameType: e});
			},
			getgamecode(e){
				this.$refs.curd.search = Object.assign({}, this.$refs.curd.search, {page: 1, productType: e});
			}
		}
	})
</script>
</body>
</html>