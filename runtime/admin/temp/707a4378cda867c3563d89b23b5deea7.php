<?php /*a:3:{s:50:"E:\project\unggame\app\admin\view\index\index.html";i:1673340034;s:52:"E:\project\unggame\app\admin\view\common\header.html";i:1673339169;s:52:"E:\project\unggame\app\admin\view\common\footer.html";i:1672902372;}*/ ?>
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
	<el-container class="el-index">
		<el-aside :width="asideWidth">
	        <div class="header">
	        	<img class="logo" src="/admin/images/logo_site.png">
	        	<span class="title">游戏管理后台</span>
	        	<i class="iconfont collapse" :class="isCollapse ? 'icon-caidanyou' : 'icon-caidanzuo01'" @click="isCollapse = !isCollapse"></i>
	        </div>
	        <el-menu class="menu" :default-active="path" :collapse="isCollapse" text-color="#fff" active-text-color="#fff" @select="clickMenu">
	            <template v-for="(item, index) in menuTree" :key="index">
	                <template v-if="item.children">
	                    <el-submenu :index="item.path" :key="item.path">
	                        <template slot="title">
	                            <img class="el-menu-icon" :src="item.icon" />
	                            <span slot="title">{{item.title}}</span>
	                            <span v-if="item.unread" class="is-dot"><span></span></span>
	                        </template>
	                        <template v-for="(subItem,subIndex) in item.children" :key="subIndex">
	                            <el-submenu v-if="subItem.children" :index="subItem.path" :key="subItem.path">
	                                <template slot="title">
		                                {{subItem.title}}
		                                <span v-if="subItem.unread" class="is-dot"><span></span></span>
		                            </template>
	                                <el-menu-item v-for="(threeItem,i) in subItem.children" :key="i" :index="threeItem.path">
	                                    {{threeItem.title}}
	                                    <span v-if="threeItem.unread" class="is-dot"><span></span></span>
	                                </el-menu-item>
	                            </el-submenu>
	                            <el-menu-item v-else :index="subItem.path" :key="subItem.path">
	                                {{subItem.title}}
	                                <span v-if="subItem.unread" class="is-dot"><span></span></span>
	                            </el-menu-item>
	                        </template>
	                    </el-submenu>
	                </template>
	                <template v-else>
	                    <el-menu-item :index="item.path" :key="item.path">
	                        <img class="el-menu-icon" :src="item.icon" />
	                        <span slot="title">{{item.title}}</span>
	                        <span v-if="item.unread" class="is-dot"><span></span></span>
	                    </el-menu-item>
	                </template>
	            </template>
	        </el-menu>
	    </el-aside>
		<el-container>
			<el-header>
			    <div class="el-tabs-warp">
        		    <el-tabs type="card" v-model="path" @tab-remove="removeTab" closable>
        	            <el-tab-pane v-for="(item, index) in tabs" :key="item.path" :name="item.path" :label="item.title"></el-tab-pane>
        	        </el-tabs>
    	        </div>
    	        <div class="el-tool">
    	        	<span class="item menu" @click="isCollapse = !isCollapse">
	    	        	<span class="icon el-icon-menu"></span>
	    	        </span>
    				<a href="/" target="_blank" class="item">
    				    <el-tooltip content="访问首页" placement="bottom">
    					    <span class="icon el-icon-monitor"></span>
    				    </el-tooltip>
    				</a>
    				<span class="item" @click="cacheClear()">
    				    <el-tooltip content="清除缓存" placement="bottom">
    					    <span class="icon el-icon-refresh"></span>
    				    </el-tooltip>
    				</span>
    				<!-- <span class="item" @click="systemCheck()">
    				    <el-tooltip content="检测更新" placement="bottom">
    					    <span class="icon el-icon-warning-outline"></span>
    				    </el-tooltip>
    				</span> -->
    				<el-dropdown class="el-userinfo-dropdown" @command="userClick">
    					<div>
    						<el-avatar size="medium" :src="userInfo.cover">
    							<img src="/admin/images/avatar.png"/>
    						</el-avatar>
    						<span>{{userInfo.nickname}}</span>
    						<i class="el-icon-arrow-down el-icon--right"></i>
    					</div>
    					<el-dropdown-menu slot="dropdown">
    						<el-dropdown-item command="personal">个人中心</el-dropdown-item>
    						<el-dropdown-item command="logout">退出登录</el-dropdown-item>
    					</el-dropdown-menu>
    				</el-dropdown>
    			</div>
		    </el-header>
	        <el-main><iframe id="iframe" :src="admin_url(path)"></iframe></el-main>
		</el-container>
        <!--版本更新-->
    	<el-dialog 
    		top="120px" 
    		width="800px"
    		:title="'当前版本：'+version" 
    		:visible.sync="dialogCheck" 
    		:close-on-click-modal="false">
            <div v-loading="loadingCheck">
                <div v-if="versionList.length > 0">
                    <el-timeline v-for="(item, index) in versionList">
                        <el-timeline-item :timestamp="item.c_version" placement="top">
                            <el-card>
                                <div v-html="item.content"></div>
                                <p style="margin-top: 10px">版本提交于 {{item.create_time}}</p>
                            </el-card>
                        </el-timeline-item>
                    </el-timeline>
                </div>
                <div v-else>
                    已经是最新版本啦~
                </div>
            </div>
            <span slot="footer" class="dialog-footer">
                <el-button size="small" @click="dialogCheck = false">关 闭</el-button>
                <el-button v-if="versionList.length > 0" size="small" type="primary" @click="systemUpdate()" :disabled="loadingUpdate">
                    {{loadingUpdate ? loadingTitle : '开始更新'}}
                </el-button>
            </span>
        </el-dialog>
	</el-container>
</div>
<script> 
	var parentVm = new Vue({
		el: '#app',
		data () {
	        return {
	        	menu: <?php echo json_encode($menu); ?>,
	            version: "<?php echo htmlentities($version); ?>",
	            drawer: false,
	            userInfo: userInfo,
	            versionList: [],
	            tabs: [],
	            path: "",
	            animate: false,
	            isCollapse: localStorage.isCollapse == 'true',
	            dialogCheck: false,
	            loadingCheck: false,
	            loadingUpdate: false,
	            loadingTitle: '开始更新',
	            updateUrl: 'index/update',
	            cacheClearUrl: 'index/cacheClear',
	            checkUpdateUrl: 'index/checkUpdate',
	        }
	    },
	    computed: {
	    	asideWidth() {
	    		return this.isCollapse ? document.body.clientWidth > 768 ? '64px' : '0' : '266px'; 
	    	},
	    	menuTree() {
	    		return tree.convert(this.menu);
	    	},
	    },
	    mounted () {
            let iframe = document.querySelector('#iframe');
            if (iframe.attachEvent) {
                iframe.attachEvent('onload', function () { NProgress.done(); })
            } else {
                iframe.onload = function () { NProgress.done(); }
            }
        },
	    created() {
	    	this.init();
	    	setInterval(this.showMarquee, 4000);
	    },
	    methods: {
	    	/**
	    	 * 初始化
	    	 */
	    	init() {
	    		// 控制台选项
	    		let consoleIndex = common.arrayIndex(this.menu, 'console/index', 'path');
	    		if (consoleIndex !== -1) {
	    			this.tabs.push(this.menu[consoleIndex]);
	    		}
	    		// 当前路由选项
		        let route = window.location.hash === "" ? '#console/index' : window.location.hash;
				this.path = route.replace('#', '');
	    	},
	        /**
	         * 点击菜单
	         * @param  {Object} path 路径
	         */
	        clickMenu(path) {
	            if (path !== this.path) {
	            	this.path = path;
	            }
	        },
	        /**
	         * 删除导航项
	         * @param  {String} targetName 导航项
	         */
	        removeTab(targetName) {
	            let index = common.arrayIndex(this.tabs, targetName, 'path');
	            this.tabs.splice(index, 1);
	            if (targetName === this.path) this.path = this.tabs[index-1]['path'];
	        },
	        /**
	         * 用户选择项
	         * @param  {Object} item
	         */
	        userClick(item) {
	            switch (item) {
                    case 'personal':
                        this.clickMenu('admin/personal');
                        break;
                    case 'logout':
                        location.href = admin_url('login/index');
                        break;
                }
	        },
	        /**
	         * 系统更新
	         */
	        systemUpdate(key = 0) {
	            let self = this;
	            let cversion = self.versionList[key]['c_version'];
	            let version  = self.versionList[key]['version'];
	            self.loadingUpdate = true;
	            self.loadingTitle  = '开始安装：' + cversion;
	            request.post(self.updateUrl,{version: version},function(res){
	                if (res.status === 'success') {
	                    if (res.isnew === 1) {
	                        self.loadingTitle = '更新完成';
	                        setTimeout(() => {
			                    location.reload();
			                }, 1000);
	                    } else {
	                        self.loadingTitle = '更新至：' + cversion;
	                        key++;
	                        self.systemUpdate(key);
	                    }
	                }
	            })
	        },
	        /**
	         * 检测更新
	         */
	        systemCheck() {
	        	let self = this;
	        	self.dialogCheck = true;
                self.loadingCheck = true;
                request.post(self.checkUpdateUrl,{},function(res){
                    if (res.status === 'success') {
                        self.versionList = res.data;
                    } else {
                        self.$notify({ showClose: true, message: res.message, type: res.status });
                    }
                    self.loadingCheck = false;
                })
	        },
	        /**
	         * 访问站点
	         */
	        visitSite() {
	        	window.open('/');
	        },
	        /**
	         * 清除缓存
	         */
	        cacheClear() {
	        	let self = this;
	        	request.post(self.cacheClearUrl,{},function(res){
                    self.$notify({ showClose: true, message: res.message, type: res.status });
                })
	        },
	    },
	    watch: {
	    	path(path) {
	    		window.location.hash = path;
	    		let pathIndex = common.arrayIndex(this.menu, path, 'path');
                let tabsIndex = common.arrayIndex(this.tabs, path, 'path');
                if (pathIndex != -1) {
                    if (tabsIndex === -1) {
                        let tab = pathIndex == -1 && path == 'console/index' ? {title: '控制台', path: 'console/index'} : this.menu[pathIndex];
                        this.tabs.push(tab);
                    }
                }
                NProgress.start();
	    	},
	    	isCollapse(v){
	    	    localStorage.isCollapse = v;
	    	},
	    }
	})
	
	// 以下为 layim 最新版写法
	   layui.config({
	     layimPath: '/admin/layui/' // 设置 layim.js 所在目录
	     ,layimResPath: '/admin/layim/' // layim 资源文件所在目录
	   }).extend({
	     layim: layui.cache.layimPath + 'layim' // 设置 layim 组件所在的路径
	   }).use(['layim','jquery'], function(layim){ // 加载组件
			$.get("https://game.unicgm.com/api/customer/mkCustomerSet/index",{account:localStorage.getItem('admin_account')},function(res){
				var friendlist=[];
				// 单独会话模式
				var socket = new WebSocket('wss://game.unicgm.com/wss');
				socket.onopen = function(){
					//发送登录信息
					  socket.send(JSON.stringify({
					    type: 'login', //随便定义，用于在服务端区分消息类型
					    client_name:res.data.mine.username,
					    room_id:1,
					    identity:2,
					    account:localStorage.getItem('admin_account')
					  }));
				};
				//收到的消息事件
				socket.onmessage = function(res){
					var mydata;
					let ondata = JSON.parse(res.data);
					var domain = "https://"+window.location.host;
					var friendone;
					if(ondata.type=='login'){
						mydata=ondata;
						// console.log(ondata);
						if(ondata.hasOwnProperty("client_list")){
							// ondata.client_list.forEach(function(item,index){
							// 	console.log(item);
							// 	console.log(index);
							// })
							var clientlist = Object.values(ondata.client_list);
							clientlist.forEach(function(item,index){
								if(item.client_id!=ondata.client_id){
									friendone={ //分组下的好友列表
										type: 'friend'
										,username: item.client_name //好友昵称
										,id: item.client_id//好友ID
										,groupid:1
										,avatar: domain+item.cover //好友头像
										,sign: item.uid //好友签名
										,status: "online" //若值为offline代表离线，online或者不填为在线
									};
								}
								layim.addList(friendone);
							})
						}else{
							if(ondata.identity==1 ){
								friendone={ //分组下的好友列表
									type: 'friend'
									,username: ondata.client_name //好友昵称
									,id: ondata.client_id//好友ID
									,groupid:1
									,avatar: domain+ondata.userinfo.cover //好友头像
									,sign: ondata.userinfo.id //好友签名
									,status: "online" //若值为offline代表离线，online或者不填为在线
								};
							}
							layim.addList(friendone);
						}
					}else if(ondata.type=='ping'){
						socket.send('{"type":"pong"}');
					}else if(ondata.type=='logout'){
						layim.removeList({
						  type: 'friend' //或者group
						  ,id: ondata.from_client_id //好友或者群组ID
						});
					}else if(ondata.type=='say'){
						// if(ondata.mine!=1){
							
						// }
						console.log(domain+ondata.userinfo.cover);
						layim.getMessage({
						  username: ondata.from_client_name //消息来源用户名
						  ,avatar: domain+ondata.userinfo.cover //消息来源用户头像
						  ,id: ondata.from_client_id //消息的来源ID（如果是私聊，则是用户id，如果是群聊，则是群组id）
						  ,type: "friend" //聊天窗口来源类型，从发送消息传递的to里面获取
						  ,content: ondata.content //消息内容
						  ,cid: 0 //消息id，可不传。除非你要对消息进行一些操作（如撤回）
						  ,mine: false //是否我发送的消息，如果为true，则会显示在右方
						  ,fromid: ondata.from_client_id //消息的发送者id（比如群组中的某个消息发送者），可用于自动解决浏览器多窗口时的一些问题
						  ,timestamp: ondata.time*1000 //服务端时间戳毫秒数。注意：如果你返回的是标准的 unix 时间戳，记得要 *1000
						});
					}
				};
				layim.config({
					 brief: true, // 是否为单独会话模式（如果 true 则不显示主面板）
					 // isfriend:false,
					 isgroup:false,
					 notice:true,
					 init:{
						 mine:res.data.mine,
						 friend:[{
							groupname:'客户',
							id:'1',
							list:friendlist
						}]
					 }
				});
				var mydata=res.data.mine;
				layim.on('sendMessage', function(res){
				  var mine = res.mine; //包含我发送的消息及我的信息
				  var to = res.to; //对方的信息
				
				  //触发到上述消息后，就可以轻松地发送socket了，如：
				  socket.send(JSON.stringify({
				    type: 'say', //随便定义，用于在服务端区分消息类型
				    to_client_id: to.id,
					to_client_name: to.name,
					content: mine.content,
					room_id:1,
					uid:mydata.id,
					identity:2
				  })); 
				  
				});
				// layim.chat({
				// 	 name: '一个新窗口'
				// 	 ,type: 'friend'
				// 	 ,avatar: 'http://tp1.sinaimg.cn/5619439268/180/40030060651/1'
				// 	 ,id: -2
				// })
			})
	   });
</script>
</body>
</html>