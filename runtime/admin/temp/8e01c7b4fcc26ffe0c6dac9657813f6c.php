<?php /*a:3:{s:62:"/www/wwwroot/game.uswindltd.com/app/admin/view/game/brand.html";i:1663920336;s:65:"/www/wwwroot/game.uswindltd.com/app/admin/view/common/header.html";i:1663904303;s:65:"/www/wwwroot/game.uswindltd.com/app/admin/view/common/footer.html";i:1650617578;}*/ ?>
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
        :search-date="false" 
        :table-sort="{prop: 'id', order: 'asc'}">
    </el-curd>
</div>
<script>
    new Vue({
        el: '#app',
        data() {
            return {
                field: [
                    {
                        label: '编号', 
                        prop: 'id',
                        table: false,
                    },
                    {
                        label: '名称', 
                        prop: 'name', 
                        table: {sort: true},
                        form:{
                            rules: [
                                {required: true,message: '名称不能为空'},
                            ]
                        }
                    },
                    {
                        label: '品牌代码', 
                        prop: 'code', 
                        table: {sort: true},
                        form:{
                            rules: [
                                {required: true,message: '名称不能为空'},
                            ]
                        }
                    },
                    {
                        label: '游戏总数', 
                        prop: 'totalCount', 
                        form:{
                            type: 'number',
                            default: 0,
                            rules: [
                                {required: true,message: '积分不能为空'},
                            ]
                        }
                    }
                ]
            }
        },
    })
</script>
</body>
</html>