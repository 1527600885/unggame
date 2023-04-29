/**
 * 编辑器
 */
Vue.component('el-editor', {
    template: `
        <div class="el-editor" v-loading="loading">
            <div :id="id">{{content}}</div>
            <el-file-dialog
                v-model="fileDialog"
                :list="fileList"
                :type="fileType"
                :title="fileTitle"
                :limit="fileLimit"
                @success-selected="fileSuccess($event)">
            </el-file-dialog>
            <el-dialog :visible.sync="collectionDialog" width="540px" :close-on-click-modal="false" append-to-body>
                <el-form 
                    ref="collectionRuleForm" 
                    :model="collectionForm" 
                    :rules="collectionRules" 
                    :inline="true"
                    @submit.native.prevent>
                    <el-form-item prop="url">
                        <el-input style="width:500px" placeholder="请输入微信文章链接" v-model="collectionForm.url"></el-input>
                    </el-form-item>
                </el-form>
                <span slot="footer" class="dialog-footer">
                    <el-button size="small" @click="collectionDialog = false">取 消</el-button>
                    <el-button size="small" type="primary" @click="collection()" :loading="collectionLoading">
                    {{collectionLoading ? '采集中，请耐心等待...' : '开始采集'}}
                    </el-button>
                </span>
            </el-dialog>
        </div>
    `,
    props: {
        value: {
            type: String,
            default: "",
        },
        height: {
            type: Number,
            default: 400,
        },
        disabled: {
            type: Boolean,
            default: false,
        },
        placeholder: {
            type: String,
            default: '请输入内容...'
        },
        editorcss: {
            type: String,
            default: ''
        },
    },
    data() {
        return {
            uploadUrl: 'api/upload',
            id: common.id(6),
            loading: true,
            fileDialog: false,
            fileType: 'image',
            fileTitle: "",
            fileLimit: 0,
            fileList: [],
            content: this.value,
            editor: {},
            collectionDialog: false,
            collectionLoading: false,
            collectionForm: {
                url: "",
            },
            collectionRules: {
                url: [
                    { required: true, message: '请输入微信文章链接', trigger: 'blur' },
                    { pattern: /^((http|https):\/\/)?(([A-Za-z0-9]+-[A-Za-z0-9]+|[A-Za-z0-9]+)\.)+([A-Za-z]+)[/\?\:]?.*$/, message: '必须以http://或https://开头', trigger: 'blur' },
                ],
            },
        }
    },
    mounted() {
		console.log(this.value);
        var self = this;
        tinymce.init({
            selector: `#${self.id}`,
            readonly : self.disabled, 
            height: self.height,
            placeholder: self.placeholder,
            menubar: false,
            icons: 'custom',
            language:'zh_CN',
            toolbar_mode:'Wrap',
            plugins: `codesample autolink link wordcount code fullscreen table imagetools lists paste hr`,
            toolbar: `removeformat undo redo selectall title fontselect fontsizeselect forecolor backcolor bold underline italic strikethrough subscript superscript align lineheight indent numlist bullist hr uploadImage link table uploadVideo uploadAudio uploadPdf codesample insertCollection code fullscreen`,
            fontsize_formats: '12px 14px 16px 18px 20px 22px 24px 26px 28px 30px 32px 48px',
            lineheight_formats: '1 1.5 1.75 2 2.5 3 4 5',
            content_css: '/themes/' + theme + '/static/css/onekey.min.css',
            content_style: this.editorcss,
            forced_root_block: 'p',
            paste_data_images: true,
            images_file_types: 'jpeg,jpg,png,gif,bmp,webp',
            // 复制word需要powerpaste插件
            end_container_on_empty_block: true,
            powerpaste_allow_local_images: true,
            powerpaste_word_import: 'prompt',
            powerpaste_html_import: 'prompt',
            // 开启域名图片模式（方便app小程序调用）
            relative_urls : false, 
            // remove_script_host : false,
            images_upload_handler:function(blobInfo, success, failure, progress) {
                let formData = new FormData();
                let file = blobInfo.blob();
                if (typeof file.name == 'undefined') {
                    file.name = blobInfo.filename();
                }
                formData.append('file', file, file.name);
                $.ajax({
                    url: admin_url(self.uploadUrl),
                    type: 'post',
                    data: formData,
                    async:false,
                    cache:false,
                    contentType:false,
                    processData:false,
                    success:function(res) {
                        if (res.status === 'login') location.reload();
                        if (res.status === 'success') {
                            let content = self.editor.getContent();
                            self.editor.setContent(content.replace('data:' + blobInfo.blob().type + ';base64,' + blobInfo.base64(), res.data.url));
                        } else {
                            self.$notify({ showClose: true, message: res.message, type: res.status});
                        }
                    },
                    error:function(res) {
                        self.$notify({ showClose: true, message: '系统错误', type: 'error'});
                    }
                })
            },
            setup: (editor) => {
                editor.on('init', () => {
                    self.editor = editor;
                    self.editor.setContent(self.content);
                    self.loading = false;
                })
                editor.on('input change undo redo execCommand KeyUp', (e) => {
                    let content  = self.editor.getContent();
                    self.content = content;
                    self.$emit('input', content);
                })
                editor.on('blur', (e) => {
                    self.$emit('change');
                });
                editor.ui.registry.addButton('insertCollection', {
                    icon: 'collection',
                    tooltip: '采集文章自动保存图片内容',
                    onAction: function () {
                       self.collectionDialog = true;
                    }
                });
                editor.ui.registry.addButton('uploadImage', {
                    icon: 'image',
                    tooltip: '插入图片',
                    onAction: function () {
                        self.fileType = 'image';
                        self.fileTitle = '插入图片';
                        self.fileDialog = true;
                        self.fileLimit = 0;
                    }
                });
                editor.ui.registry.addButton('uploadVideo', {
                    icon: 'video',
                    tooltip: '插入视频',
                    onAction: function () {
                        self.fileType = 'video';
                        self.fileTitle = '插入视频';
                        self.fileDialog = true;
                        self.fileLimit = 1;
                    }
                });
                editor.ui.registry.addButton('uploadAudio', {
                    icon: 'audio',
                    tooltip: '插入音频',
                    onAction: function () {
                        self.fileType = 'audio';
                        self.fileTitle = '插入音频';
                        self.fileDialog = true;
                        self.fileLimit = 1;
                    }
                });
                editor.ui.registry.addButton('uploadPdf', {
                    icon: 'pdf',
                    tooltip: '插入PDF',
                    onAction: function () {
                        self.fileType = 'other';
                        self.fileTitle = '插入PDF';
                        self.fileDialog = true;
                        self.fileLimit = 1;
                    }
                });
            }
        });
    },
    methods: {
        fileSuccess(list) {
            let html = "";
            switch(this.fileType) {
                case 'image':
                    list.forEach(function (item, index){
                        // let iamgeurl = 'https://d3luh14arnema3.cloudfront.net'+`${item.url}`;
                        html += `<img src="${item.url}">`;
                    })
                    break;
                case 'video':
                    list.forEach(function (item, index){
                        html += `<video src="${item.url}" controls="controls"></video>`;
                    })
                    break;
                case 'audio':
                    list.forEach(function (item, index){
                        html += `<audio src="${item.url}" controls="controls"></audio>`;
                    })
                    break;
                case 'other':
                    list.forEach(function (item, index){
                        html += `<iframe src="${item.url}" style="width:100%;height:500px;" scrolling="no" frameborder="0" allowfullscreen="allowfullscreen" webkitallowfullscreen="true" mozallowfullscreen="true"></iframe>`;
                    })
                    break;
            }
            this.fileList = [];
            this.editor.insertContent(html);
        },
        /**
         * 采集
         */
        collection() {
            let self = this;
            self.$refs.collectionRuleForm.validate((valid) => {
                if (valid) {
                    self.collectionLoading = true;
                    request.post('tinymceeditor/api/index' , self.collectionForm, function (res) {
                        self.collectionLoading = false;
                        if (res.status === 'success') {
                            self.editor.setContent(res.content);
                            self.collectionDialog = false;
                        } else {
                            self.$notify.error(res.message);
                        }
                    })
                } else {
                    return false;
                }
            });
        },
    },
})