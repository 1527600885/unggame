<?php /*a:3:{s:62:"/www/wwwroot/game.uswindltd.com/app/admin/view/curd/index.html";i:1668043125;s:65:"/www/wwwroot/game.uswindltd.com/app/admin/view/common/header.html";i:1663904303;s:65:"/www/wwwroot/game.uswindltd.com/app/admin/view/common/footer.html";i:1650617578;}*/ ?>
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
    <div class="el-curd">
        <el-form 
            ref="search" 
            class="el-curd-header" 
            size="small"
            :inline="true" 
            :model="search" 
            @submit.native.prevent>
            <el-form-item>
                <el-tooltip placement="bottom">
                    <div slot="content">
                        从数据库中获取最新的数据表、数据表字段
                    </div>
                    <el-button type="info" icon="el-icon-refresh" @click="location.href=location.href">同步数据库</el-button>
                </el-tooltip>
                <el-button 
                    type="warning" 
                    icon="el-icon-plus" 
                    :disabled="rows.length === 0" 
                    @click="code = true">
                    生成应用
                </el-button>
                <el-button 
                    type="danger" 
                    icon="el-icon-delete" 
                    :disabled="rows.length === 0" 
                    @click="removeData()">
                    删除
                </el-button>
                <a style="margin-left:10px" href="<?php echo config('app.api'); ?>/1090/108.html" target="_blank">
                    <el-button icon="el-icon-question" size="small">帮助</el-button>
                </a>
            </el-form-item>
            <el-form-item>
                <el-input 
                    style="width:300px" 
                    placeholder="关键词搜索" 
                    v-model="search.keyword" 
                    @keyup.enter.native="getData()">
                    <el-button slot="append" icon="el-icon-search" @click="getData()"></el-button>
                </el-input>
            </el-form-item>
        </el-form>
        <el-table
            ref="table"
            :data="table" 
            :default-sort="{prop: search.prop, order: search.order}"
            v-loading="loading"
            @selection-change="selectionChange">
            <el-table-column type="selection" width="55"></el-table-column>
            <el-table-column prop="name" label="表名"></el-table-column>
            <el-table-column prop="title" label="标题"></el-table-column>
            <el-table-column prop="sort" label="排序"></el-table-column>
            <el-table-column prop="plugin" label="插件目录"></el-table-column>
            <el-table-column prop="number" label="生成次数"></el-table-column>
            <el-table-column label="操作">
                <template slot-scope="scope">
                    <el-tooltip content="编辑" placement="top">
                        <el-button type="warning" size="mini" icon="el-icon-edit" circle @click="openData(scope.row)"></el-button>
                    </el-tooltip>
                    <el-tooltip content="删除" placement="top">
                        <el-button type="danger" size="mini" icon="el-icon-delete" circle @click="removeData(scope.row)"></el-button>
                    </el-tooltip>
                </template>
            </el-table-column>
        </el-table>
    </div>
    <el-drawer :visible.sync="code" :with-header="false" size="100%">
        <el-page-header @back="code=false" content="生成插件">
            <template v-slot:title>Esc键返回</template>
        </el-page-header>
        <div class="el-pane-warp">
            <el-form :model="codeData" :rules="codeRules" ref="codeData" label-width="100px">
                <el-form-item label="菜单图标：" prop="cover">
                    <el-file-select v-model="codeData.cover"></el-file-select>
                    <div class="tips">请<a target="_blank" href="https://www.iconfont.cn/">点击此处</a>选择菜单图标，建议图片16*16像素PNG格式</div>
                </el-form-item>
                <el-form-item label="应用标识：" prop="name">
                    <el-input v-model="codeData.name" placeholder="如：ceshi"></el-input>
                    在public/plugins下生成{{codeData.name}}应用目录
                </el-form-item>
                <el-form-item label="应用名称：" prop="title">
                    <el-input v-model="codeData.title" placeholder="如：测试"></el-input>
                </el-form-item>
                <el-form-item label="已选中表：" prop="table">
                    <el-empty v-if="rows.length === 0" description="数据表空空如也~"></el-empty>
                    <div v-for="(item, index) in rows">
                        <el-tooltip content="点击取消关联" placement="top">
                            <div style="cursor: pointer;display: inline-block;" @click="removeTable(item, index)">
                                <i 
                                    style="margin-right: 10px;font-size: 20px;vertical-align: middle;"
                                    :class="item.name.indexOf('mk_' + codeData.name) == -1 ? 'el-icon-close' : 'el-icon-check'" 
                                    :style="{color: item.name.indexOf('mk_' + codeData.name) == -1 ? '#F56C6C' : '#67C23A'}">
                                </i>
                                {{item.name}}
                            </div>
                        </el-tooltip>
                        <div v-if="item.name.indexOf('mk_' + codeData.name) == -1" style="color: #F56C6C">
                            表前缀不合格，表前缀应为mk_{{codeData.name}}{{rows.length > 1 ? '_' : ''}}
                        </div>
                    </div>
                </el-form-item>
                <el-form-item label="注意事项：">
                    <p>1.根据单或多个表生成一个应用，包含控制器、模型、视图、菜单权限、API接口</p>
                    <p>2.重复生成应用只会覆盖admin/view视图</p>
                    <p>3.查看更多操作请阅读<a href="<?php echo config('app.api'); ?>/1090/108.html" target="_blank">《文档手册》</a></p>
                </el-form-item>
            <el-form>
            <div class="el-bottom">
                <el-button 
                    size="medium" 
                    type="primary" 
                    icon="el-icon-refresh-right" 
                    :loading="codeLoading" 
                    :disabled="codeDisabled"
                    @click="getCode()">
                    生 成
                </el-button>
                <el-button size="medium" @click="code = false">取 消</el-button>
            </div>
        </div>
    </el-drawer>
    <el-drawer :visible.sync="drawer" :with-header="false" size="100%">
        <el-page-header @back="drawer=false" content="表详情">
            <template v-slot:title>Esc键返回</template>
        </el-page-header>
        <div class="el-layout">
            <div class="el-pane-warp">
                <div class="el-curd-field">
                    <div class="tabs">
                        <el-tabs v-model="set">
                            <el-tab-pane name="form" label="表单信息">
                                <draggable class="add-draggable" v-model="field" v-bind="addDraggable" :clone="addField">
                                    <div v-for="(item, index) in field" class="el-curd-field-move-item">
                                        <i class="iconfont" :class="item.icon"></i>
                                        <div class="title">{{item.title}}</div>
                                    </div>
                                </draggable>
                            </el-tab-pane>
                            <el-tab-pane name="table" label="表格信息">
                                <el-form 
                                    ref="drawerData" 
                                    label-width="100px"
                                    :model="drawerData" 
                                    :rules="drawerRules">
                                    <el-form-item label="标题：" prop="title">
                                        <el-input v-model="drawerData.title" placeholder="例如：产品"></el-input>
                                    </el-form-item>
                                    <el-form-item label="名称：" prop="name">
                                        <el-input v-model="drawerData.name" placeholder="例如：mk_app_ceshi_product"></el-input>
                                    </el-form-item>
                                    <el-form-item label="每页：" prop="table_page_size">
                                        <el-input v-model="drawerData.table_page_size" type="number" min="1"></el-input>
                                    </el-form-item>
                                    <el-form-item label="排序：" prop="table_sort">
                                        <el-select v-model="drawerData.table_sort">
                                            <el-option value="" label="默认"></el-option>
                                            <el-option
                                                v-for="(item, index) in drawerData.field"
                                                :value="item.prop"
                                                :label="item.label">
                                            </el-option>
                                        </el-select>
                                    </el-form-item>
                                    <el-form-item label="树形：" prop="table_tree">
                                        <el-switch v-model="drawerData.table_tree" :active-value="1" :inactive-value="0"></el-switch>
                                    </el-form-item>
                                    <el-form-item label="导出：" prop="table_export">
                                        <el-switch v-model="drawerData.table_export" :active-value="1" :inactive-value="0"></el-switch>
                                    </el-form-item>
                                    <el-form-item label="模糊查询：" prop="search_keyword">
                                       <el-switch v-model="drawerData.search_keyword" :active-value="1" :inactive-value="0"></el-switch>
                                    </el-form-item>
                                    <el-form-item label="日期查询：" prop="search_date">
                                       <el-switch v-model="drawerData.search_date" :active-value="1" :inactive-value="0"></el-switch>
                                    </el-form-item>
                                    <el-form-item label="分类查询：" prop="search_catalog">
                                       <el-parameter v-model="drawerData.search_catalog" :rank="false" :push="false"></el-parameter>
                                    </el-form-item>
                                    <el-form-item label="状态查询：" prop="search_status">
                                       <el-parameter v-model="drawerData.search_status" :rank="false" :push="false"></el-parameter>
                                    </el-form-item>
                                </el-form>
                            </el-tab-pane>
                        </el-tabs>
                    </div>
                    <div class="preview">
                        <el-form class="el-preview-form" v-if="set == 'form'" :label-width="drawerData.form_label_width + 'px'">
                            <el-row>
                                <draggable class="draggable" :class="{dragin: drawerData.field.length === 0}" v-model="drawerData.field" v-bind="draggable">
                                    <template v-for="(item, index) in drawerData.field">
                                        <el-col
                                            class="block"
                                            :key="index" 
                                            :md="item.colMd === '' ? drawerData.form_col_md : item.colMd"
                                            :class="{active: item.prop == fieldForm.prop}">
                                            <div class="curd-icon">
                                                <i class="el-icon-edit" @click="openField(item)" title="编辑"></i>
                                                <i class="rank el-icon-rank" title="移动"></i>
                                                <i class="el-icon-delete" @click="moveField(item, index)" title="删除"></i>
                                            </div>
                                            <el-form-item 
                                                :prop="item.prop" 
                                                :label="item.label"
                                                :rules="item.required ? [{ required: true, message: '不能为空'}] : []">
                                                <div v-if="item.child.length == 0 && (item.is === 'el-radio-group' || item.is === 'el-checkbox-group')">
                                                    请配置表单选项
                                                </div>
                                                <component 
                                                    v-else
                                                    v-model="item.default"
                                                    :is="item.is"
                                                    :key="item.id"
                                                    :type="item.type"
                                                    :options="item.options"
                                                    :disabled="item.disabled"
                                                    :placeholder="item.placeholder"
                                                    :filterable="item.filterable"
                                                    :multiple="item.multiple"
                                                    :active-value="1"
                                                    :inactive-value="0"
                                                    :style="{opacity: item.formShow ? 1 : 0.2}"
                                                    format="yyyy-MM-dd HH:mm:ss"
                                                    value-format="yyyy-MM-dd HH:mm:ss"
                                                    start-placeholder="开始日期"
                                                    range-separator="至"
                                                    end-placeholder="结束日期">
                                                    <template v-if="item.is == 'el-radio-group' || item.is == 'el-checkbox-group' || item.is == 'el-select'">
                                                        <template v-if="item.is == 'el-radio-group' || item.is == 'el-checkbox-group'">
                                                            <component 
                                                                v-for="(val, key) in item.child"
                                                                :is="item.is == 'el-radio-group' ? 'el-radio' : 'el-checkbox'"
                                                                :key="val.value"
                                                                :label="val.value">
                                                                {{ val.title }}
                                                            </component>
                                                        </template>
                                                        <template v-else>
                                                            <el-option 
                                                                v-for="(val, key) in item.child"
                                                                :key="val.value"
                                                                :value="val.value"
                                                                :label="val.title">
                                                                {{ val.title }}
                                                            </el-option>
                                                        </template>
                                                    </template>
                                                </component>
                                                <div class="el-tips" v-if="item.tips !== ''" v-html="item.tips"></div>
                                            </el-form-item> 
                                        </el-col>
                                    </template>
                                </draggable>
                            </el-row>
                        </el-form>
                        <template v-else>
                            <div class="el-curd">
                                <el-form ref="search" class="el-curd-header" size="small" :inline="true">
                                    <el-form-item class="el-button-form">
                                        <el-button type="info" icon="el-icon-refresh">刷新</el-button>
                                        <el-button v-if="drawerData.table_expand == 1 && drawerData.table_tree == 1" icon="el-icon-arrow-down">展开</el-button>
                                        <el-button type="danger" icon="el-icon-delete" disabled>删除</el-button>
                                        <el-button type="primary" icon="el-icon-plus">添加</el-button>
                                        <el-dropdown v-if="drawerData.table_export == 1">
                                            <el-button type="success" icon="el-icon-download">导出</el-button>
                                            <el-dropdown-menu slot="dropdown">
                                                <el-dropdown-item command="csv">CSV</el-dropdown-item>
                                                <el-dropdown-item command="json">JSON</el-dropdown-item>
                                                <el-dropdown-item command="xhtml">XHTML</el-dropdown-item>
                                                <el-dropdown-item command="txt">TXT</el-dropdown-item>
                                            </el-dropdown-menu>
                                        </el-dropdown>
                                    </el-form-item>
                                    <el-form-item prop="date" v-if="drawerData.search_date == 1">
                                        <el-date-picker 
                                            type="daterange" 
                                            align="right" 
                                            unlink-panels 
                                            range-separator="至" 
                                            start-placeholder="开始日期" 
                                            end-placeholder="结束日期" 
                                            format="yyyy-MM-dd" 
                                            value-format="yyyy-MM-dd">
                                        </el-date-picker>
                                    </el-form-item>
                                    <el-form-item prop="keyword" v-if="drawerData.search_keyword == 1">
                                        <el-input placeholder="根据关键词搜索">
                                            <el-button slot="append" icon="el-icon-search"></el-button>
                                        </el-input>
                                    </el-form-item>
                                    <el-form-item prop="catalog" v-if="drawerData.search_catalog.length > 0">
                                        <el-select placeholder="查看所有分类目录" filterable>
                                            <el-option label="全部分类" value=""></el-option>
                                            <el-option v-for="(item, index) in drawerData.search_catalog" :key="index" :label="item.title" :value="item.value"></el-option>
                                        </el-select>
                                    </el-form-item>
                                    <el-form-item prop="status" v-if="drawerData.search_status.length > 0">
                                        <el-select placeholder="查看所有状态">
                                            <el-option label="全部状态" value=""></el-option>
                                            <el-option v-for="(item, index) in drawerData.search_status" :key="index" :label="item.title" :value="item.value"></el-option>
                                        </el-select>
                                    </el-form-item>
                                </el-form>
                                <el-table>
                                    <el-table-column type="selection" width="55"></el-table-column>
                                    <template v-for="(item, index) in drawerData.field" >
                                        <el-table-column
                                            v-if="item.tableShow && item.prop != 'id'"
                                            :prop="item.tableProp"
                                            :sortable="item.tableSort"
                                            :width="item.tableWidth > 0 ? item.tableWidth + 'px' : ''">
                                            <template slot="header" slot-scope="scope">
                                                <div class="column" :class="{active: item.prop == fieldForm.prop}">
                                                    {{item.tableLabel}}
                                                    <i class="el-icon-edit" title="编辑" @click="openField(item)"></i>
                                                    <i class="el-icon-delete" title="删除" @click="moveField(item, index)"></i>
                                                </div>
                                            </template>
                                        </el-table-column>
                                    </template>
                                </el-table>
                            </div>
                        </template>
                    </div>
                    <div class="set">
                        <el-form label-width="55px">
                            <el-empty v-if="fieldForm == ''" description="未选择字段"></el-empty>
                            <template v-else>
                                <template v-if="set === 'form'">
                                    <el-form-item prop="prop">
                                        <template v-slot:label>
                                            <el-tooltip content="请慎重修改，数据表字段列名" placement="top">
                                                <i>字段：</i>
                                            </el-tooltip>
                                        </template>
                                        <el-input v-model="fieldForm.prop"></el-input>
                                    </el-form-item>
                                    <el-form-item prop="label">
                                        <template v-slot:label>
                                            <el-tooltip content="表格/表单继承此标题" placement="top">
                                                <i>标题：</i>
                                            </el-tooltip>
                                        </template>
                                        <el-input v-model="fieldForm.label"></el-input>
                                    </el-form-item>
                                    <el-form-item prop="formShow">
                                        <template v-slot:label>
                                            <el-tooltip content="不显示则表单中不会出现此字段，演示只是做隐藏处理" placement="top">
                                                <i>显示：</i>
                                            </el-tooltip>
                                        </template>
                                        <el-switch v-model="fieldForm.formShow"></el-switch>
                                    </el-form-item>
                                    <el-form-item label="组件：" prop="is">
                                        <el-select v-model="fieldForm.is" @change="isChange()" filterable>
                                            <el-option label="输入框" value="el-input"></el-option>
                                            <el-option label="编辑器" value="el-editor"></el-option>
                                            <el-option label="单选框" value="el-radio-group"></el-option>
                                            <el-option label="多选框" value="el-checkbox-group"></el-option>
                                            <el-option label="选择框" value="el-select"></el-option>
                                            <el-option label="文件选择" value="el-file-select"></el-option>
                                            <el-option label="多文件选择" value="el-file-list-select"></el-option>
                                            <el-option label="开关" value="el-switch"></el-option>
                                            <el-option label="日期时间选择器" value="el-date-picker"></el-option>
                                            <el-option label="时间选择器" value="el-time-select"></el-option>
                                            <el-option label="计数器" value="el-input-number"></el-option>
                                            <el-option label="级联选择器（多级联动）" value="el-cascader"></el-option>
                                            <el-option label="滑块" value="el-slider"></el-option>
                                            <el-option label="评分" value="el-rate"></el-option>
                                            <el-option label="颜色选择器" value="el-color-picker"></el-option>
                                            <el-option label="链接选择" value="el-link-select"></el-option>
                                            <el-option label="自定义参数" value="el-parameter"></el-option>
                                        </el-select>
                                    </el-form-item>
                                    <el-form-item prop="placeholder" v-if="fieldForm.is == 'el-input' || fieldForm.is == 'el-editor' || fieldForm.is == 'el-select'">
                                        <template v-slot:label>
                                            <el-tooltip content="表单占用提示信息" placement="top">
                                                <i>提示：</i>
                                            </el-tooltip>
                                        </template>
                                        <el-input v-model="fieldForm.placeholder"></el-input>
                                    </el-form-item>
                                    <el-form-item prop="tips">
                                        <template v-slot:label>
                                            <el-tooltip content="表单下方指示信息，引导作用" placement="top">
                                                <i>tips：</i>
                                            </el-tooltip>
                                        </template>
                                        <el-input v-model="fieldForm.tips"></el-input>
                                    </el-form-item>
                                    <el-form-item 
                                        prop="child" 
                                        v-if="fieldForm.is == 'el-radio-group' || fieldForm.is == 'el-checkbox-group' || fieldForm.is == 'el-select'">
                                        <template v-slot:label>
                                            <el-tooltip content="单选框、多选框、选择框的子选项" placement="top">
                                                <i>选项：</i>
                                            </el-tooltip>
                                        </template>
                                        <el-parameter v-model="fieldForm.child" :rank="false" :push="false"></el-parameter>
                                    </el-form-item>
                                    <el-form-item 
                                        label="类型：" 
                                        prop="type" 
                                        v-if="fieldForm.is == 'el-file-select' || fieldForm.is == 'el-file-list-select' || fieldForm.is == 'el-date-picker'">
                                        <el-radio-group 
                                            v-model="fieldForm.type" 
                                            v-if="fieldForm.is == 'el-file-select' || fieldForm.is == 'el-file-list-select'">
                                            <el-radio label="all">全部</el-radio>
                                            <el-radio label="image">图片</el-radio>
                                            <el-radio label="video">视频</el-radio>
                                            <el-radio label="audio">音频</el-radio>
                                            <el-radio label="word">文档</el-radio>
                                            <el-radio label="other">其它</el-radio>
                                        </el-radio-group>
                                        <el-radio-group 
                                            v-model="fieldForm.type" 
                                            v-if="fieldForm.is == 'el-date-picker'">
                                            <el-radio label="date">日期</el-radio>
                                            <el-radio label="datetime">日期时间</el-radio>
                                            <el-radio label="daterange">日期范围</el-radio>
                                            <el-radio label="datetimerange">日期时间范围</el-radio>
                                        </el-radio-group>
                                    </el-form-item>
                                    <el-form-item v-if="fieldForm.is != 'el-link-select' && fieldForm.is != 'el-parameter'" prop="default">
                                        <template v-slot:label>
                                            <el-tooltip content="新增数据时，此字段的默认值" placement="top">
                                                <i>默认：</i>
                                            </el-tooltip>
                                        </template>
                                        <template v-if="fieldForm.is == 'el-switch'">
                                            <el-radio-group v-model="fieldForm.default">
                                                <el-radio :label="1">开启</el-radio>
                                                <el-radio :label="0">关闭</el-radio>
                                            </el-radio-group>
                                        </template>
                                        <template v-else>
                                            <template v-if="typeof fieldForm.default == 'object'">
                                                <el-input 
                                                    style="margin-bottom: 10px" 
                                                    v-for="(val,key) in fieldForm.default" 
                                                    v-model="fieldForm.default[key]" 
                                                    placeholder="请输入内容">
                                                    <el-button 
                                                        slot="append" 
                                                        icon="el-icon-delete-solid" 
                                                        size="small"
                                                        @click="fieldForm.default.splice(key, 1)">
                                                    </el-button>
                                                </el-input>
                                                <el-button 
                                                    icon="el-icon-plus" 
                                                    size="small"
                                                    @click="fieldForm.default.push('')"
                                                    circle>
                                                </el-button>
                                            </template>
                                            <el-input v-else v-model="fieldForm.default"></el-input>
                                        </template>
                                    </el-form-item>
                                    <el-form-item prop="multiple" v-if="fieldForm.is == 'el-select'">
                                        <template v-slot:label>
                                            <el-tooltip content="当组件为选择框时，可指定是否能选多个项目" placement="top">
                                                <i>多选：</i>
                                            </el-tooltip>
                                        </template>
                                        <el-switch v-model="fieldForm.multiple"></el-switch>
                                    </el-form-item>
                                    <el-form-item prop="filterable" v-if="fieldForm.is == 'el-select'">
                                        <template v-slot:label>
                                            <el-tooltip content="当组件为选择框时，可指定是否能搜索项目" placement="top">
                                                <i>搜索：</i>
                                            </el-tooltip>
                                        </template>
                                        <el-switch v-model="fieldForm.filterable"></el-switch>
                                    </el-form-item>
                                    <el-form-item label="必填：" prop="required">
                                        <el-switch v-model="fieldForm.required"></el-switch>
                                    </el-form-item>
                                    <el-form-item label="禁用：" prop="disabled">
                                        <el-switch v-model="fieldForm.disabled"></el-switch>
                                    </el-form-item>
                                    <el-form-item prop="colMd">
                                        <template v-slot:label>
                                            <el-tooltip content="栅格化布局，1/2代表2个表单为1行以此类推，为空则继承父级" placement="top">
                                                <i>栅格：</i>
                                            </el-tooltip>
                                        </template>
                                        <el-radio-group v-model="fieldForm.colMd">
                                            <el-radio label="">继承</el-radio>
                                            <el-radio :label="24">1&nbsp;/&nbsp;1</el-radio>
                                            <el-radio :label="12">1&nbsp;/&nbsp;2</el-radio>
                                            <el-radio :label="8">1&nbsp;/&nbsp;3</el-radio>
                                            <el-radio :label="6">1&nbsp;/&nbsp;4</el-radio>
                                            <el-radio :label="4">1&nbsp;/&nbsp;6</el-radio>
                                            <el-radio :label="2">1&nbsp;/&nbsp;12</el-radio>
                                        </el-radio-group>
                                    </el-form-item>
                                    <el-form-item prop="pattern" v-if="fieldForm.is == 'el-input'">
                                        <template v-slot:label>
                                            <el-tooltip content="数据正则验证" placement="top">
                                                <i>验证：</i>
                                            </el-tooltip>
                                        </template>
                                        <el-input v-model="fieldForm.pattern"></el-input>
                                        <el-radio-group v-model="fieldForm.pattern">
                                            <el-radio label="/^1[3456789]\d{9}$/">手机</el-radio>
                                            <el-radio label="/^\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/">邮箱</el-radio>
                                            <el-radio label="/^\d{6}(18|19|20)?\d{2}(0[1-9]|1[12])(0[1-9]|[12]\d|3[01])\d{3}(\d|X)$/">身份证</el-radio>
                                            <el-radio label="/^([hH][tT]{2}[pP]:\/\/|[hH][tT]{2}[pP][sS]:\/\/)(([A-Za-z0-9-~]+)\.)+([A-Za-z0-9-~\/])+$/">网址</el-radio>
                                            <el-radio label="/^[\u0391-\uFFE5]+$/">纯中文</el-radio>
                                            <el-radio label="/^[a-zA-Z]+$/">纯英文</el-radio>
                                            <el-radio label="/^[0-9]*$/">纯字母</el-radio>
                                        </el-radio-group>
                                        
                                    </el-form-item>
                                </template>
                                <template v-if="set == 'table'">
                                    <el-form-item prop="tableProp">
                                        <template v-slot:label>
                                            <el-tooltip placement="top">
                                                <div slot="content">
                                                    显示其它字段内容，当前'{{fieldForm.prop}}'字段，但我想显示'c_{{fieldForm.prop}}'字段的内容
                                                </div>
                                                <i>字段：</i>
                                            </el-tooltip>
                                        </template>
                                        <el-input v-model="fieldForm.tableProp"></el-input>
                                    </el-form-item>
                                    <el-form-item prop="tableLabel">
                                        <template v-slot:label>
                                            <el-tooltip placement="top">
                                                <div slot="content">
                                                    显示其它字段标题，如'{{fieldForm.label}}'标题，但我想显示'自定义'标题
                                                </div>
                                                <i>标题：</i>
                                            </el-tooltip>
                                        </template>
                                        <el-input v-model="fieldForm.tableLabel"></el-input>
                                    </el-form-item>
                                    <el-form-item prop="tableShow">
                                        <template v-slot:label>
                                            <el-tooltip content="不显示则表格中不会出现此字段，演示只是做隐藏处理" placement="top">
                                                <i>显示：</i>
                                            </el-tooltip>
                                        </template>
                                        <el-switch v-model="fieldForm.tableShow"></el-switch>
                                    </el-form-item>
                                    <el-form-item prop="tableSort">
                                        <template v-slot:label>
                                            <el-tooltip content="表格可根据此列排序" placement="top">
                                                <i>排序：</i>
                                            </el-tooltip>
                                        </template>
                                        <el-switch v-model="fieldForm.tableSort"></el-switch>
                                    </el-form-item>
                                    <el-form-item prop="width">
                                        <template v-slot:label>
                                            <el-tooltip content="表格占用列宽度，0代表自动宽度" placement="top">
                                                <i>宽度：</i>
                                            </el-tooltip>
                                        </template>
                                        <el-input type="number" v-model="fieldForm.width"></el-input>
                                    </el-form-item>
                                    <el-form-item prop="bind">
                                        <template v-slot:label>
                                            <el-tooltip content="此列绑定其它字段一起显示" placement="top">
                                                <i>绑定：</i>
                                            </el-tooltip>
                                        </template>
                                        <el-input 
                                            style="margin-bottom: 10px" 
                                            v-for="(val,key) in fieldForm.bind" 
                                            v-model="fieldForm.bind[key]" 
                                            placeholder="字段名称">
                                            <el-button 
                                                slot="append" 
                                                icon="el-icon-delete-solid" 
                                                size="small"
                                                @click="fieldForm.bind.splice(key, 1)">
                                            </el-button>
                                        </el-input>
                                        <el-button 
                                            icon="el-icon-plus" 
                                            size="small"
                                            @click="fieldForm.bind.push('')"
                                            circle>
                                        </el-button>
                                    </el-form-item>
                                </template>
                            </template>
                        </el-form>
                    </div>
                </div>
                <div class="el-bottom">
                    <el-button size="medium" type="primary" icon="el-icon-refresh-right" @click="saveData()" :loading="drawerLoading">保 存</el-button>
                    <el-button size="medium" @click="drawer = false">返 回</el-button>
                </div>
            </div>
        </div>
    </el-drawer>
</div>
<script>
    new Vue({
        el: '#app',
        data() {
            return {
                set: 'form',
                row: {},
                rows: [],
                table: [],
                field: [
                    {title: '输入框', is: 'el-input', icon: 'icon-danhangshurukuang'},
                    {title: '编辑器', is: 'el-editor', icon: 'icon-fuwenbenbianjiqi_zhonghuaxian'},
                    {title: '单选框', is: 'el-radio-group', icon: 'icon-danxuankuang'},
                    {title: '多选框', is: 'el-checkbox-group', icon: 'icon-duoxuan_xuanzhong'},
                    {title: '选择器', is: 'el-select', icon: 'icon-xuanzeqi'},
                    {title: '文件选择', is: 'el-file-select', icon: 'icon-a-wenjianjiawenjian'},
                    {title: '多文件选择', is: 'el-file-list-select', icon: 'icon-wenjian1'},
                    {title: '开关', is: 'el-switch', icon: 'icon-kaiguan'},
                    {title: '日期时间选择器', is: 'el-date-picker', icon: 'icon-riqi'},
                    {title: '时间选择器', is: 'el-time-select', icon: 'icon-shijian1'},
                    {title: '计数器', is: 'el-input-number', icon: 'icon-shuzishurukuang'},
                    {title: '多级联动', is: 'el-cascader', icon: 'icon-cengji'},
                    {title: '滑块', is: 'el-slider', icon: 'icon-huakuai'},
                    {title: '评分', is: 'el-rate', icon: 'icon-pingfen'},
                    {title: '颜色选择器', is: 'el-color-picker', icon: 'icon-yanse1'},
                    {title: '链接选择', is: 'el-link-select', icon: 'icon-lianjie'},
                    {title: '自定义参数', is: 'el-parameter', icon: 'icon-chanpincanshu'},
                    // 你可以自定义组件...
                ],
                code: false,
                codeData: {
                    name: '',
                    title: '',
                    cover: '',
                },
                codeRules: {
                    name: [
                        { required: true, message: '应用标识不能为空', trigger: 'blur' },
                        // { pattern: /^[a-zA-Z]+$/, message: '应用标识只能为纯英文', trigger: 'blur'},
                    ],
                    title: [
                        { required: true, message: '应用名称不能为空', trigger: 'blur' },
                    ],
                    cover: [
                        { required: true, message: '应用菜单图标不能为空', trigger: 'blur' },
                    ],
                },
                codeLoading: false,
                drawer:false, 
                loading: false,
                synchroLoading: false,
                search:{
                    keyword: '',
                },
                fieldForm: '',
                drawerLoading: false,
                drawerData: {
                    field: []
                },
                drawerRules: {
                    title: [
                        { required: true, message: '表标题不能为空', trigger: 'blur' },
                        { min: 2, max: 50, message: '在2-50个字符串之间', trigger: 'blur' }
                    ],
                    name: [
                        { required: true, message: '表名称不能为空', trigger: 'blur' },
                        { min: 2, max: 50, message: '在2-50个字符串之间', trigger: 'blur' },
                    ],
                    sort: [
                        { required: true, message: '不能为空', trigger: 'blur' },
                    ],
                    field: [
                        { required: true, message: '不能为空', trigger: 'blur' },
                    ],
                    form_label_width: [
                        { required: true, message: '不能为空', trigger: 'blur' },
                    ],
                    form_col_md: [
                        { required: true, message: '不能为空', trigger: 'blur' },
                    ],
                    table_tree: [
                        { required: true, message: '不能为空', trigger: 'blur' },
                    ],
                    table_expand: [
                        { required: true, message: '不能为空', trigger: 'blur' },
                    ],
                    table_export: [
                        { required: true, message: '不能为空', trigger: 'blur' },
                    ],
                    table_page_size: [
                        { required: true, message: '不能为空', trigger: 'blur' },
                    ],
                    table_operation_width: [
                        { required: true, message: '不能为空', trigger: 'blur' },
                    ],
                    search_keyword: [
                        { required: true, message: '不能为空', trigger: 'blur' },
                    ],
                    search_date: [
                        { required: true, message: '不能为空', trigger: 'blur' },
                    ],
                    preview: [
                        { required: true, message: '不能为空', trigger: 'blur' },
                    ],
                },
                indexUrl: 'curd/index',
                updateUrl: 'curd/update',
                codeUrl: 'curd/code',
                deleteUrl: 'curd/delete',
                deleteFieldUrl: 'curd/deleteField',
                saveFieldUrl: 'curd/saveField',
                updateFieldUrl: 'curd/updateField',
                addDraggable: {
                    animation: 300,
                    forceFallback: true,
                    sort: false,
                    group: {name: 'people', pull: 'clone', put: false},
                },
                draggable: {
                    handle: '.rank',
                    animation: 300,
                    forceFallback: true,
                    group:"people"
                },
            }
        },
        created () {
            this.getData();
        },
        computed: {
            codeDisabled() {
                let self   = this;
                let status = false;
                self.rows.forEach(function (item, index) {
                    if (item.name.indexOf('mk_' + self.codeData.name) == -1) {
                        status = true;
                    }
                });
                if (self.rows.length < 1) {
                    return true;
                }
                return status;
            }
        },
        methods: {
            /**
             * 打开字段
             */
            openField(item) {
                this.fieldForm = item;
            },
            /**
             * 生成字段
             */
            addField(item) {
                item.prop       = common.id(6);
                item.label      = '未命名';
                item.colMd      = '';
                if (item.is == 'el-checkbox-group' 
                || item.is == 'el-file-list-select' 
                || item.is == 'el-parameter'
                || item.is == 'el-field') {
                    item.default = [];
                } else {
                    item.default = '';
                }
                item.placeholder= '';
                item.filterable = true;
                item.multiple   = true;
                item.type       = '';
                if (item.is == 'el-file-select' || item.is == 'el-file-list-select') {
                    item.type = 'image';
                }
                if (item.is == 'el-date-picker') {
                    item.type = 'date';
                }
                if (item.is == 'el-time-select') {
                    item.type = 'time';
                }
                item.child      = [];
                item.tips       = '';
                item.required   = false;
                item.pattern    = '';
                item.disabled   = false;
                item.formShow   = true;
                item.tableWidth = 0;
                item.tableBind  = [];
                item.tableSort  = true;
                item.tableProp  = item.prop;
                item.tableLabel = item.label;
                item.tableShow  = true;
                this.fieldForm  = JSON.parse(JSON.stringify(item));
                return this.fieldForm;
            },
            /**
             * 修改字段
             */
            updateField(item, index) {
                request.post(this.updateFieldUrl, {table: this.drawerData.name, prop: item.prop}, function(res){});
            },
            /**
             * 删除字段
             */
            moveField(item, index) {
                let self = this;
                self.fieldForm  = '';
                self.drawerData.field.splice(index, 1);
            },
            /**
             * 生成代码
             */
            getCode() {
                let self = this;    
                self.$refs.codeData.validate((valid) => {
                    if (valid) {
                        let data = self.codeData;
                        data['table'] = self.rows;
                        self.codeLoading = true;
                        request.post(self.codeUrl, data, function(res) {
                            self.codeLoading = false;
                            if (res.status === 'success') {
                                self.getData();
                                self.code = false;
                            }
                            self.$notify({ showClose: true, message: res.message, type: res.status});
                        });
                    } else {
                        return false;
                    }
                });
            },
            /**
             * 取消关联
             */
            removeTable(item, index) {
                this.rows.splice(index, 1);
                this.$refs.table.toggleRowSelection(item, false);
            },
            /**
             * 获取数据
             */
            getData() {
                let self = this;    
                self.loading = true;
                request.post(self.indexUrl, self.search, function(res) {
                    self.loading = false;
                    if (res.status === 'success') {
                        res.data.forEach(function (item, index) {
                            item.check = true;
                        })
                        self.table           = res.data;
                        parent.parentVm.menu = res.publicMenu;
                    } else {
                        self.$notify({ showClose: true, message: res.message, type: res.status});
                    }
                });
            },
            /**
             * 打开数据
             */
            openData(row) {
                this.drawer     = true;
                this.type       = 'form';
                this.fieldForm  = '';
                row.form_label_width = row.form_label_width < 100 ? 100 : row.form_label_width;
                row.field.forEach(function (item, index){
                    item.is         = typeof item.is == 'undefined' ? 'el-input' : item.is;
                    item.prop       = typeof item.prop == 'undefined' ? common.id(6) : item.prop;
                    item.label      = typeof item.label == 'undefined' ? item.prop : item.label;
                    item.colMd      = typeof item.colMd == 'undefined' ? '' : item.colMd;
                    item.default    = typeof item.default == 'undefined' ? '' : item.default;
                    item.placeholder= typeof item.placeholder == 'undefined' ? '' : item.placeholder;
                    item.filterable = typeof item.filterable == 'undefined' ? true : item.filterable;
                    item.multiple   = typeof item.multiple == 'undefined' ? true : item.multiple;
                    item.type       = typeof item.type == 'undefined' ? '' : item.type;
                    item.child      = typeof item.child == 'undefined' ? [] : item.child;
                    item.tips       = typeof item.tips == 'undefined' ? '' : item.tips;
                    item.required   = typeof item.required == 'undefined' ? false : item.required;
                    item.pattern    = typeof item.pattern == 'undefined' ? '' : item.pattern;
                    item.disabled   = typeof item.disabled == 'undefined' ? false : item.disabled;
                    item.formShow   = typeof item.formShow == 'undefined' ? true : item.formShow;
                    item.tableWidth = typeof item.tableWidth == 'undefined' ? 0 : item.tableWidth;
                    item.tableBind  = typeof item.tableBind == 'undefined' ? [] : item.tableBind;
                    item.tableSort  = typeof item.tableSort == 'undefined' ? true : item.tableSort;
                    item.tableProp  = typeof item.tableProp == 'undefined' ? item.prop : item.tableProp;
                    item.tableLabel = typeof item.tableLabel == 'undefined' ? item.label : item.tableLabel;
                    item.tableShow  = typeof item.tableShow == 'undefined' ? true : item.tableShow;
                })
                this.drawerData = JSON.parse(JSON.stringify(row));
            },
            /**
             * 保存数据
             */
            saveData(formName) {
                let self = this;
                self.$refs.drawerData.validate((valid) => {
                    if (valid) {
                        self.drawerLoading = true;
                        request.post(self.updateUrl, self.drawerData, function(res){
                            self.drawerLoading = false;
                            if (res.status === 'success') {
                                self.getData();
                                self.drawer = false;
                            }
                            self.$notify({ showClose: true, message: res.message, type: res.status});
                        });
                    } else {
                        return false;
                    }
                });
            },
            /**
             * 删除
             */
            removeData(row = "") {
                let self = this;
                let ids  = row === "" ? common.arrayColumn(self.rows, 'id') : [row.id]; 
                self.$confirm('确定删除数据吗？', '', { confirmButtonText: '确定', cancelButtonText: '取消', type: 'warning'}).then(() => {
                    request.post(self.deleteUrl, {'ids': ids}, function(res){
                        if (res.status === 'success') {
                            self.getData();
                            self.$emit('remove-data', res);
                        }
                        self.$notify({ showClose: true, message: res.message, type: res.status});
                    });
                }).catch(() => {});
            },
            /**
             * 更换表单类型
             */
            isChange() {
                if (this.fieldForm.is == 'el-checkbox-group' 
                || this.fieldForm.is == 'el-file-list-select' 
                || this.fieldForm.is == 'el-parameter'
                || this.fieldForm.is == 'el-field') {
                    this.fieldForm.default = [];
                } else {
                    this.fieldForm.default = '';
                }
                if (this.fieldForm.is == 'el-file-select' || this.fieldForm.is == 'el-file-list-select') {
                    this.fieldForm.type = 'image';
                }
                if (this.fieldForm.is == 'el-date-picker') {
                    this.fieldForm.type = 'date';
                }
                if (this.fieldForm.is == 'el-time-select') {
                    this.fieldForm.type = 'time';
                }
                if (this.fieldForm.is == 'el-input') {
                    this.fieldForm.type = '';
                }
            },
            /**
             * 选中行
             */
            selectionChange(rows) {
                this.rows = rows;
            },
        },
    })
</script>
</body>
</html>