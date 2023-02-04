/**
 * 编辑器
 */
Vue.component('el-editor', {
    template: `
        <div class="el-editor" v-loading="loading">
            <div :id="id">{{content}}</div>
            <el-upload
                style="display:none"
                ref="upload"
                :show-file-list="false"
                :action="uploadUrl"
                :data="{token: localStorage.userToken}"
                :on-success="successUpload"
                :on-error="errorUpload">
            </el-upload>
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
        placeholder: {
            type: String,
            default: '请输入内容...'
        },
    },
    data() {
        return {
            uploadUrl: index_url('api/user/upload'),
            id: this.randId(),
            loading: true,
            content: this.value,
            editor: {},
        }
    },
    mounted() {
        var self = this;
        tinymce.init({
            selector: `#${self.id}`,
            height: self.height,
            readonly : self.disabled, 
            placeholder: self.placeholder,
            menubar: false,
            icons: 'custom',
            language:'zh_CN',
            toolbar_mode:'Wrap',
            plugins: `codesample autolink link wordcount fullscreen table imagetools lists paste`,
            toolbar: `removeformat undo redo selectall fontselect fontsizeselect forecolor backcolor bold underline italic strikethrough subscript superscript align lineheight indent numlist bullist uploadImage link table uploadVideo uploadAudio uploadPdf codesample fullscreen`,
            fontsize_formats: '12px 14px 16px 18px 20px 22px 24px 26px 28px 30px 32px 48px',
            lineheight_formats: '1 1.5 1.75 2 2.5 3 4 5',
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
                var formData = new FormData();
                formData.append('file', blobInfo.blob());
                formData.append('token',localStorage.userToken);
                $.ajax({
                    url: self.uploadUrl,
                    type: 'post',
                    data: formData,
                    async:false,
                    cache:false,
                    contentType:false,
                    processData:false,
                    success:function(res) {
                        if (res.status === 'login') {
                            location.href = index_url("login/index")
                        }
                        if (res.status === 'success') {
                            let content = self.editor.getContent();
                            self.editor.setContent(content.replace('data:' + blobInfo.blob().type + ';base64,' + blobInfo.base64(), res.data));
                        } else {
                            self.$notify({ showClose: true, message: res.message, type: res.status});
                        }
                    },
                    error:function(res) {
                        self.$notify({ showClose: true, message:'系统错误', type: 'error'});
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
                editor.ui.registry.addButton('uploadImage', {
                    icon: 'image',
                    tooltip: '插入图片',
                    onAction: function () {
                        document.querySelector(".el-upload").click()
                    }
                });
            }
        });
    },
    methods: {
        /**
         * 随机id
         */
        randId() {
            var chars = 'ABCDEFGHJKMNPQRSTWXYZabcdefhijkmnprstwxyz2345678';
            var id = '';
            for (i = 0; i < 16; i++) {
                id += chars.charAt(Math.floor(Math.random() * chars.length));
            }
            return id;
        },
        /**
         * 上传成功回调
         * @param  {Object} res   返回当前状态
         * @param  {Object} item  当前文件
         */
        successUpload(res, item) {
            var self = this;
            if (res.status === 'success') {
                self.editor.insertContent(`<img src="${res.data}">`);
            } else {
                if (res.status === 'login') location.href = index_url("login/index");
                self.$notify({showClose: true, message: res.message, type: res.status});
            }
        },
        /**
         * 上传错误回调
         * @param  {Object} err 错误信息
         */
        errorUpload(err) {
            this.$notify({showClose: true, message: '系统错误', type: 'error'});
        },
    },
    watch: {
        value(v) {
            this.content = v;
        }
    }
})