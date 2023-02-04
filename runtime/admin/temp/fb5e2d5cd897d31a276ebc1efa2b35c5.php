<?php /*a:3:{s:59:"/www/wwwroot/www.unicgm.com/app/admin/view/admin/index.html";i:1663147838;s:61:"/www/wwwroot/www.unicgm.com/app/admin/view/common/header.html";i:1670144435;s:61:"/www/wwwroot/www.unicgm.com/app/admin/view/common/footer.html";i:1670201344;}*/ ?>
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
        :table-sort="{prop: 'create_time', order: 'desc'}"
        :search-date="false" 
        :search-status="[{label: '正常', value: 1}, {label: '屏蔽', value: 0}]">
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
                        prop: 'cover', 
                        label: '头像', 
                        table: {label: '资料', is: 'image',width: '70px'},
                        form: {is: 'el-file-select',type: 'image'},
                    },
                    {
                        prop: 'account', 
                        label: '账号', 
                        table: {label: '',width: '250px', bind: ['email']},
                        form: {
                            rules: [
                                {required: true, message: '不能为空'},
                                {pattern: /^[^\u4e00-\u9fa5]+$/, message: '不能包含中文字符'}
                            ],
                        },
                    },
                    {
                        prop: 'email', 
                        label: '邮箱', 
                        table: false,
                        form: {
                            rules: [
                                {required: true, message: '不能为空'},
                            ],
                        },
                    },
                    {
                        prop: 'nickname', 
                        label: '昵称', 
                        table: false,
                        form: {
                            rules: [
                                {required: true, message: '不能为空'},
                            ],
                        },
                    },
                    {
                        prop: 'login_count', 
                        label: '登录次数', 
                        table:{sort: true},
                        form: false,
                    },
                    {
                        prop: 'group_id', 
                        label: '所属组别',
                        table: {prop: 'group_title', sort: true},
                        form: {
                            is: 'el-select', 
                            child: {is: 'el-option',value: <?php echo json_encode($group); ?>, props:{label: 'title', value: 'id'}},
                            rules: [
                                {required: true, message: '不能为空'},
                            ],
                        },
                    },
                    {
                        prop: 'password', 
                        label: '密码', 
                        table: false, 
                        form: {
                            type: 'password',
                            placeholder: '不修改密码则此处为空',
                            rules: [
                                {saveRequired: true, message: '新增管理员时，密码不能为空'},
                                {pattern: /^[^\u4e00-\u9fa5]+$/, message: '不能包含中文字符'}
                            ],
                        },
                    },
                    {
                        prop: 'status',
                        label: '状态',  
                        table: {is: 'el-switch', sort: true}, 
                        form: {
                            is: 'el-switch',
                            default: 1, 
                        },
                    },
                    {
                        prop: 'create_time', 
                        label: '注册时间', 
                        table:{label: '注册&登录时间',sort: true, bind: ['login_time'], width: '160px'},
                        form: false,
                    },
                ],
            }
        },
    })
</script>
</body>
</html>