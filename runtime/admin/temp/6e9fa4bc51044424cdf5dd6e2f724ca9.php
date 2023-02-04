<?php /*a:3:{s:66:"/www/wwwroot/game.uswindltd.com/app/admin/view/admin/personal.html";i:1662427096;s:65:"/www/wwwroot/game.uswindltd.com/app/admin/view/common/header.html";i:1663904303;s:65:"/www/wwwroot/game.uswindltd.com/app/admin/view/common/footer.html";i:1650617578;}*/ ?>
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
    <div class="el-layout">
        <div class="el-pane-warp">
            <el-form
                :model="formData" 
                ref="formData" 
                :rules="formRules" 
                label-width="70px">
                <el-form-item label="头像：" prop="cover">
                    <el-file-select v-model="formData.cover"></el-file-select>
                </el-form-item>
                <el-form-item label="昵称：" prop="nickname">
                    <el-input v-model="formData.nickname" placeholder="请输入管理员昵称" maxlength="40" show-word-limit></el-input>
                </el-form-item>
                <el-form-item label="邮箱：" prop="email">
                    <el-input v-model="formData.email" placeholder="请输入管理员邮箱"></el-input>
                </el-form-item>
                <el-form-item label="账号：" prop="account">
                    <el-input 
                        v-model="formData.account" 
                        placeholder="请输入管理员登录账号" 
                        maxlength="40" 
                        show-word-limit 
                        :disabled="true">
                    </el-input>
                </el-form-item>
                <el-form-item label="密码：" prop="password">
                    <el-input v-model="formData.password" placeholder="留空则为原密码"></el-input>
                </el-form-item>
                <el-form-item>
                    <el-button 
                        type="primary" 
                        size="small" 
                        icon="el-icon-refresh-right"
                        @click="saveForm()" 
                        :loading="formLoading">
                        保存资料
                    </el-button>
                </el-form-item>
            </el-form>
        </div>
    </div>
</div>
<script>
    new Vue({
        el: '#app',
        data() {
            return {
                formData: userInfo,
                formRules: {
                    nickname: [
                        { required: true, message: '请输入管理员昵称', trigger: 'blur' },
                        { min: 2, max: 40, message: '长度在 2 到 40 个字符', trigger: 'blur' }
                    ],
                    email: [
                        { required: true, message: '请输入管理员邮箱', trigger: 'blur' },
                        { type: 'email', message: '请输入正确的邮箱地址', trigger: 'blur' }
                    ],
                    account: [
                        { required: true, message: '请输入管理员登录账号', trigger: 'blur' },
                        { min: 5, max: 40, message: '长度在 5 到 40 个字符', trigger: 'blur' }
                    ],
                },
                formLoading: false,
            }
        },
        created () {
            this.formData.password = "";
        },
        methods: {
            /**
             * 保存表单
             */
            saveForm() {
                let self = this;
                self.$refs.formData.validate((valid) => {
                    if (valid) {
                        self.formLoading = true;
                        request.post("admin/personal", self.formData, function(res){
                            self.formLoading = false;
                            self.$notify({ showClose: true, message: res.message, type: res.status});
                            if (res.status === 'success') {
                                parent.parentVm.userInfo = JSON.parse(JSON.stringify(self.formData));
                            } else {
                                self.getData();
                            }
                        });
                    } else {
                        return false;
                    }
                });
            },
            /**
             * 重置表单
             */
            resetForm() {
                this.$refs.formData.resetFields();
            },
        },
    })
</script>
</body>
</html>