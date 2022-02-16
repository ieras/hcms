<!-- 所有上传组件的公共方法和数据 -->
{hcmstag:include file="admin@/components/upload/upload-file-mixin"}
<script type="text/x-template" id="select-video">
    <div class="select-file">
        <div style="max-height: 400px;">
            <el-dialog title="选择视频" @close="$emit('close')" width="668px" :visible.sync="dialogVisible">
                <div>
                    <el-row>
                        <el-col :span="6">
                            <div class="menu-container">
                                <ul role="menubar" class="el-menu">
                                    <li class="el-menu-item-group">
                                        <ul class="group_list">
                                            <li class="el-menu-item" style="padding: 0 8px;" @click="selectGroup(-1)"
                                                :class="{'group_active' : now_group == -1}">
                                                全部
                                            </li>
                                            <li class="el-menu-item" style="padding: 0 8px;" @click="selectGroup(0)"
                                                :class="{'group_active' : now_group == 0}">
                                                未分组
                                            </li>
                                            <template v-for="(item,index) in group_list">
                                                <li :key="index" class="el-menu-item"
                                                    style="padding: 0 8px;position: relative;"
                                                    :class="{'group_active' : now_group == item.group_id }">
                                            <span @click="selectGroup(item.group_id)"
                                                  style="word-break:break-all; white-space:normal; width:75%;line-height: 30px;vertical-align:middle;display:inline-block;">{{item.group_name}}</span>
                                                    <div style="position: absolute;right: 10px;top: 0;">
                                                        <el-dropdown @command="(c)=>handleGroup(c,item)" size="small"
                                                                     trigger="click">
                                                            <div class="el-dropdown-link" style="line-height: 30px;">
                                                                <i style="line-height: 20px;"
                                                                   class="el-input__icon el-icon-more-outline group_edit_icon"></i>
                                                            </div>
                                                            <el-dropdown-menu slot="dropdown">
                                                                <el-dropdown-item command="edit">
                                                                    编辑
                                                                </el-dropdown-item>
                                                                <el-dropdown-item command="delete">
                                                                    删除
                                                                </el-dropdown-item>
                                                            </el-dropdown-menu>
                                                        </el-dropdown>
                                                    </div>
                                                </li>
                                            </template>
                                        </ul>
                                        <div class="grid-content" style="padding: 19px;">
                                            <el-button type="primary" @click="editGroup()" size="mini">新增分组</el-button>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                        </el-col>

                        <el-col :span="18">
                            <div>
                                <el-upload
                                        :limit="max_upload"
                                        multiple
                                        drag
                                        :before-upload="beforeUploadEvent"
                                        :action="upload_url"
                                        :accept="file_accept"
                                        :on-success="handleUploadSuccess"
                                        :on-error="handleUploadError"
                                        :on-exceed="handleExceed"
                                        :on-progress="handleUploadProgress"
                                        :on-change="handleUploadChange"
                                        :data="uploadData"
                                        id="upload_input"
                                        ref="upload"
                                        :show-file-list="false">
                                    <div style="display: flex; justify-content: center;align-items: center;line-height: 36px;">
                                        <div>
                                            <i class="el-icon-upload"></i>
                                        </div>
                                        <div style="padding: 0 10px">
                                            <div class="el-upload__text">将文件拖到此处，或<em>点击上传</em></div>
                                        </div>
                                    </div>
                                </el-upload>
                            </div>
                            <div class="grid-content bg-purple-light" style="margin-top: 10px;">
                                <div>
                                    <template v-for="(item,index) in data_list">
                                        <div :key="index"
                                             class="img-list-item">
                                            <el-tooltip class="item" effect="dark" :content="item.file_name"
                                                        placement="top">
                                                <img :src="item.file_thumb"
                                                     style="width:80px;height: 80px;"
                                                     @click="selectFileEvent(index)">
                                            </el-tooltip>
                                            <div v-if="item.is_select" class="is_check" @click="selectFileEvent(index)">
                                                <span style="line-height: 80px;" class="el-icon-check"></span>
                                            </div>
                                        </div>
                                    </template>
                                    <div>
                                        <el-button v-show="selected_file_list.length > 0" type="danger" size="small"
                                                   @click="clickDeleteSelected">删除选中
                                        </el-button>
                                        <el-button v-show="selected_file_list.length > 0" type="primary" size="small"
                                                   @click="clickCancelSelected">取消选中
                                        </el-button>
                                        <el-select v-show="selected_file_list.length > 0" v-model="move_group_id"
                                                   placeholder="移动至" style="width:130px;margin-left: 10px;" size="small"
                                                   @change="moveFileGroup">
                                            <el-option label="请选择分组" :value="-1"></el-option>
                                            <el-option label="未分组" :value="0"></el-option>
                                            <el-option :label="item.group_name" :value="item.group_id"
                                                       v-for="(item,index) in group_list" :key="index">
                                            </el-option>
                                        </el-select>
                                    </div>
                                    <el-pagination
                                            :page-size="per_page"
                                            :current-page.sync="current_page"
                                            :total="total_num"
                                            v-show="total_num > 0"
                                            background
                                            layout="prev, pager, next"
                                            @current-change="currentChangeEvent"
                                            style="margin-top: 10px;float: right;padding-right: 50px;"
                                    ></el-pagination>
                                </div>
                            </div>
                        </el-col>
                    </el-row>
                </div>
                <div>
                    <div solt="footer" style="text-align: right;padding-top: 10px;">
                        <el-button size="small" type="primary" @click="confirmEvent">确定</el-button>
                        <el-button size="small" type="default" @click="$emit('close')">关闭</el-button>
                    </div>
                </div>
            </el-dialog>
        </div>
    </div>
</script>
<script>
    $(function () {
        Vue.component('select-video', {
            template: '#select-video',
            mixins: [window.__vue_upload_mixin],
            props: {
                show: false,
            },
            data() {
                return {
                    file_accept: "video/*",
                    max_upload: 99,
                    file_type: 'video'
                }
            },
            watch: {
                show(value) {
                    if (value) {
                        //获取分组列表
                        this.getGroupList();
                    }
                    this.dialogVisible = value
                }
            },
            computed: {},
            methods: {
            }
        });
    })

</script>

<style>
    .menu-container {
        max-height: 400px;
        overflow-y: scroll;
    }

    /* for Chrome */
    .menu-container::-webkit-scrollbar {
        display: none;
    }

    /* 上传图片    */
    .select-file .thumb-uploader .el-upload {
        border: 1px dashed #d9d9d9;
        border-radius: 6px;
        cursor: pointer;
        position: relative;
        overflow: hidden;
    }

    .select-file .thumb-uploader .el-upload:hover {
        border-color: #409EFF;
    }

    .el-upload__input {
        display: none !important;
    }

    /*图库*/
    .select-file .img-list-item {
        width: 82px;
        height: 82px;
        border: 1px dashed #d9d9d9;
        border-radius: 6px;
        display: inline-flex;
        margin-right: 10px;
        margin-bottom: 10px;
        position: relative;
        cursor: pointer;
        vertical-align: top;
    }

    .select-file .group_active {
        color: #409eff;
    }

    .select-file .is_check {
        position: absolute;
        top: 0;
        left: 0;
        width: 80px;
        height: 80px;
        text-align: center;
        background-color: rgba(0, 0, 0, 0.6);
        color: #fff;
        font-size: 40px;
    }

    .select-file .group_list {
        height: 330px;
        overflow: scroll;
        border-bottom: 1px solid gainsboro;
    }

    .select-file .el-menu {
        border: none;
    }

    .select-file .el-menu-item {
        height: 40px;
        line-height: 40px;
    }

    .select-file .el-upload-dragger {
        height: 36px;
        line-height: 30px;
        text-align: right;
        padding: 0 2px;
    }

    .select-file .el-upload-dragger .el-icon-upload {
        font-size: 18px !important;
        color: #C0C4CC;
        line-height: 22px;
        margin: 0;
    }

    .select-file .is_check {
        position: absolute;
        top: 0;
        left: 0;
        width: 80px;
        height: 80px;
        text-align: center;
        background-color: rgba(0, 0, 0, 0.6);
        color: #fff;
        font-size: 40px;
    }
</style>