<div class="page-container" v-cloak>
    <el-card>
        <div slot="header" class="breadcrumb">
            <el-breadcrumb separator="/">
                <el-breadcrumb-item>文件列表</el-breadcrumb-item>
            </el-breadcrumb>
        </div>
        <div>
            <el-form size="small" :inline="true">
                <el-form-item>
                    <el-input v-model="where.file_name" clearable placeholder="文件名称"></el-input>
                </el-form-item>
                <el-form-item>
                    <el-select v-model="where.file_type" placeholder="文件类型">
                        <el-option label="不限" value=""></el-option>
                        <el-option label="图片" value="image"></el-option>
                        <el-option label="视频" value="video"></el-option>
                        <el-option label="文档" value="doc"></el-option>
                    </el-select>
                </el-form-item>
                <el-form-item>
                    <el-link>
                        <el-button type="primary" @click="searchEvent">查询</el-button>
                    </el-link>
                </el-form-item>
            </el-form>
            <div style="margin-bottom: 10px;">
                <el-button size="small" @click="show_select_image=true" type="primary">选择图片</el-button>
                <el-button size="small" @click="show_select_video=true" type="primary">选择视频</el-button>
                <el-button size="small" @click="show_select_doc=true" type="primary">选择文档</el-button>
            </div>
        </div>
        <div>
            <el-table
                    size="small"
                    :data="data_list"
                    style="width: 100%">
                <el-table-column
                        fixed
                        prop="file_id"
                        label="ID"
                        width="80">
                </el-table-column>
                <el-table-column
                        prop="file_name"
                        label="文件名称"
                        min-width="140">
                </el-table-column>
                <el-table-column
                        align="center"
                        prop="file_type"
                        label="文件类型"
                        width="80">
                </el-table-column>
                <el-table-column
                        align="center"
                        prop="file_size"
                        label="大小(KB)"
                        width="80">
                </el-table-column>
                <el-table-column
                        prop="file_url"
                        min-width="200"
                        label="访问地址">
                </el-table-column>
                <el-table-column
                        prop="file_path"
                        min-width="200"
                        label="文件路径">
                </el-table-column>
                <el-table-column
                        prop="created_at"
                        width="140"
                        label="创建时间">
                </el-table-column>
                <el-table-column
                        fixed="right"
                        align="center"
                        min-width="80"
                        label="操作">
                    <template slot-scope="{row}">
                        <el-link @click="deleteEvent(row)">
                            <el-button size="small" type="danger">删除</el-button>
                        </el-link>
                    </template>
                </el-table-column>
            </el-table>
            <div class="pagination-container">
                <el-pagination
                        background
                        layout="prev, pager, next"
                        :total="total_num"
                        :current-page="current_page"
                        :page-size="per_page"
                        @current-change="currentChangeEvent"
                >
                </el-pagination>
            </div>
        </div>
    </el-card>
    <select-image :show="show_select_image" @confirm="selectImageConfirm"
                  @close="show_select_image=false"></select-image>
    <select-video :show="show_select_video" @confirm="selectVideoConfirm"
                  @close="show_select_video=false"></select-video>
    <select-doc :show="show_select_doc" @confirm="selectDocConfirm"
                @close="show_select_doc=false"></select-doc>
</div>
<!--引入图片选择组件-->
{hcmstag:include file="admin@/components/upload/select-image"}
<!--引入视频选择组件-->
{hcmstag:include file="admin@/components/upload/select-video"}
<!--引入文档选择组件-->
{hcmstag:include file="admin@/components/upload/select-doc"}
<script>
    $(function () {
        new Vue({
            el: ".page-container",
            data: {
                show_select_doc: false,
                show_select_image: false,
                show_select_video: false,
                is_init_list: true,
                where: {},
            },
            methods: {
                selectDocConfirm(files) {
                    console.log('selectDocConfirm', files)
                },
                selectVideoConfirm(files) {
                    console.log('selectVideoConfirm', files)
                },
                selectImageConfirm(files) {
                    console.log('selectImageConfirm', files)
                },
                GetList() {
                    this.httpGet("{:url('admin/upload/index/lists')}", {
                        page: this.current_page,
                        ...this.where
                    }).then(res => {
                        let {lists = {}} = res.data
                        this.handRes(lists)
                    })
                },
                deleteEvent({file_id}) {
                    this.$confirm("是否确认删除该记录？", '提示', {file_id}).then(() => {
                        this.httpPost("{:url('admin/upload/file/delete')}", {
                            selected_file_ids: [file_id]
                        }).then(res => {
                            if (res.status) {
                                this.$message.success(res.msg)
                                this.GetList()
                            }
                        })
                    }).catch(err => {
                    })
                },
                searchEvent() {
                    this.current_page = 1
                    this.GetList()
                }
            }
        })
    })
</script>