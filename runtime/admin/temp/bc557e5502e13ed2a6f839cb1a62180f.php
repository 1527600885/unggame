<?php /*a:3:{s:58:"/www/wwwroot/www.unicgm.com/app/admin/view/user/index.html";i:1667375663;s:61:"/www/wwwroot/www.unicgm.com/app/admin/view/common/header.html";i:1670144435;s:61:"/www/wwwroot/www.unicgm.com/app/admin/view/common/footer.html";i:1670201344;}*/ ?>
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
        :table-operation-width="250"
        :table-sort="{prop: 'create_time', order: 'desc'}"
        :search-status="[{label: '正常', value: 1}, {label: '屏蔽', value: 0}]"
        variable="userInfo"
        preview>
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
                        prop: 'email', 
                        label: '邮箱', 
                        table: {label: '', bind: ['mobile'], width: '250px'},
                        form: {
                            rules: [
                                {required: true, message: '邮箱不能为空'},
                            ],
                            colMd: 12
                        },
                    },
                    {
                        prop: 'mobile', 
                        label: '手机', 
                        table: false,
                        form: {colMd: 12}
                    },
					{
					    prop: 'whatsapp', 
					    label: 'WhatsApp', 
					    table: {sort: false},
					    form: {colMd: 12}
					},
					{
					    prop: 'telegram', 
					    label: 'Telegram', 
					    table: {sort: false},
					    form: {colMd: 12}
					},
					{
					    prop: 'line', 
					    label: 'Line', 
					    table: {sort: false},
					    form: {colMd: 12}
					},
                    {
                        prop: 'nickname', 
                        label: '昵称', 
                        table: false,
                        form: {
                            rules: [
                                {required: true, message: '昵称不能为空'},
                            ],
                            colMd: 12
                        },
                    },
                    {
                        prop: 'login_count', 
                        label: '登录次数', 
                        // table:{sort: true},
						table: false,
                        form: false,
                    },
                    {
                        prop: 'balance', 
                        label: '余额',  
                        table:{sort: true},
                        form: {type: 'number',default: 0.00,colMd: 12}
                    },
                    {
                        prop: 'password', 
                        label: '密码', 
                        table: false, 
                        form: {
                            type: 'password',
                            placeholder: '不修改密码则此处为空',
                            rules: [
                                {saveRequired: true, message: '新增用户时，密码不能为空'},
                                {pattern: /^[^\u4e00-\u9fa5]+$/, message: '不能包含中文字符'}
                            ],
                            colMd: 12
                        },
                    },
                    {
                        prop: 'now_integral', 
                        label: '积分',  
                        table:{sort: true},
                        form: {type: 'number',default: 0,colMd: 12}
                    },
                    {
                        prop: 'group_id', 
                        label: '分组',
                        table: {prop: 'group_title', sort: true},
                        form: {
                            is: 'el-select', 
                            child: {is: 'el-option',value: <?php echo json_encode($group); ?>, props:{label: 'title', value: 'id'}},
                            rules: [
                                {required: true, message: '请选择组别'},
                            ],
                            colMd: 12
                        },
                    },
                    {
                        prop: 'sex', 
                        label: '性别', 
                        table: false,
                        form: {
                            is: 'el-select',
                            default: 0, 
                            child: {is: 'el-option', value:[{label:'男', value:0},{label:'女',value:1}]},
                            colMd: 12
                        }
                    },
                    {
                        prop: 'birthday', 
                        label: '生日',
                        table: false,
                        form: {is: 'el-date-picker', type: 'date', format: 'yyyy-MM-dd', valueFormat: 'yyyy-MM-dd',colMd: 12},
                    },
                    {
                        prop: 'describe', 
                        label: '签名', 
                        table: false,
                        form: {type: 'textarea'},
                    },
                    {
                        prop: 'status',
                        label: '状态',  
                        table: {is: 'el-switch', sort: true},
                        form: {is: 'el-switch', default: 1},
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