<?php /*a:3:{s:64:"/www/wwwroot/www.unicgm.com/app/admin/view/admin_menu/index.html";i:1662107710;s:61:"/www/wwwroot/www.unicgm.com/app/admin/view/common/header.html";i:1670144435;s:61:"/www/wwwroot/www.unicgm.com/app/admin/view/common/footer.html";i:1670201344;}*/ ?>
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
        :table-sort="{prop: 'sort', order: 'desc'}"
        @get-data="refresh($event)"
        table-tree>
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
                        prop: 'icon', 
                        label: '图标', 
                        table: false,
                        form: {
                            is: 'el-file-select',
                            type: 'image',
                            tips: '请<a target="_blank" href="https://www.iconfont.cn/">点击此处</a>选择菜单图标，建议图片16*16像素PNG格式',
                        },
                    },
                    {
                        prop: 'title', 
                        label: '名称', 
                        table: {sort: true},
                        form: {
                            placeholder: '请输入标题', 
                            rules: [
                                {required: true,message: '名称不能为空'},
                            ]
                        }
                    },
                    {
                        prop: 'pid', 
                        label: '父级', 
                        table: false, 
                        form: {
                            is: 'el-select',
                            child: {is: 'el-option', value: 'this'},
                            filterable: true,
                            rules: [
                                {required: true,message: '父级不能为空'},
                            ]
                        },
                    },
                    {
                        prop: 'sort', 
                        label: '排序', 
                        table: false,
                        form: {
                            type: 'number',
                            default: 0, 
                            placeholder: '降序排序，值越大越靠前'
                        },
                    },
                    {
                        prop: 'path', 
                        label: '路径', 
                        table: {sort: true},
                        form: {
                            placeholder: '请输入控制器/方法名', 
                            rules: [
                                {required: true,message: '路径不能为空'},
                            ]
                        }
                    },
                    {
                        prop: 'ifshow',
                        label: '显示菜单',  
                        table: {sort: true, prop: 'c_ifshow'},
                        form: {
                            is: 'el-switch',
                            default: 1, 
                        },
                    },
                    {
                        prop: 'logwriting',
                        label: '写入日志',  
                        table: {sort: true, prop: 'c_logwriting'},
                        form: {
                            is: 'el-switch',
                            default: 1, 
                        },
                    },
                ],
            }
        },
        methods: {
            refresh(res) {
                parent.parentVm.menu = res.publicMenu;
            }
        }
    })
</script>
</body>
</html>