<?php /*a:3:{s:56:"E:\project\unggame\app\admin\view\admin_group\index.html";i:1672902372;s:52:"E:\project\unggame\app\admin\view\common\header.html";i:1673339169;s:52:"E:\project\unggame\app\admin\view\common\footer.html";i:1672902372;}*/ ?>
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
                        prop: 'title', 
                        table: {sort: true},
                        form:{
                            rules: [
                                {required: true,message: '名称不能为空'},
                            ]
                        }
                    },
                    {
                        prop: 'status', 
                        label: '状态',  
                        table: {is: 'el-switch', sort: true},
                        form: {
                            is: 'el-switch', 
                            default: 1,
                            rules: [
                                {required: true,message: '状态不能为空'},
                            ]
                        }
                    },
                    {
                        prop: 'role', 
                        label: '权限', 
                        table: false, 
                        form: {
                            is: 'el-tree-menu',
                            list: <?php echo json_encode($menu); ?>, 
                            default: [],
                        },
                    }
                ]
            }
        },
    })
</script>
</body>
</html>