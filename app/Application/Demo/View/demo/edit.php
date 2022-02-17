<div class="page-container" v-cloak>
    <el-card>
        <div slot="header" class="breadcrumb">
            <el-breadcrumb separator="/">
                <el-breadcrumb-item><a href="{:url('demo/demo/lists')}">列表示例</a></el-breadcrumb-item>
                <el-breadcrumb-item>{$title}</el-breadcrumb-item>
            </el-breadcrumb>
        </div>
        <div>
            <el-form size="small" label-width="80px">
                <el-form-item label="上级菜单">
                    <div>
                        <el-select v-model="form.parent_access_id">
                            <el-option :value="0" label="一级菜单"></el-option>
                        </el-select>
                    </div>
                    <div class="form-small">
                        <small>上级菜单，菜单最多三级</small>
                    </div>
                </el-form-item>
                <el-form-item label="名称">
                    <el-input v-model="form.access_name" placeholder=""></el-input>
                </el-form-item>
                <el-form-item label="图片">
                    <div v-if="form.img_url" @click="show_select_image=true">
                        <el-image class="form-upload__image" :src="form.img_url"></el-image>
                    </div>
                    <div v-else>
                        <el-button @click="show_select_image=true" type="primary">选择图片</el-button>
                    </div>
                </el-form-item>
                <el-form-item label="排序">
                    <el-input v-model="form.sort" type="number"></el-input>
                    <div class="form-small">
                        <small>数值越小，约靠前</small>
                    </div>
                </el-form-item>
                <el-form-item label="是否菜单">
                    <el-radio-group v-model="form.is_menu" size="small">
                        <el-radio :label="1">菜单</el-radio>
                        <el-radio :label="0">仅权限</el-radio>
                    </el-radio-group>
                </el-form-item>
                <el-form-item label="富文本">
                    <div>
                        <ueditor :init="''" @update="richUpdateEvent"></ueditor>
                    </div>
                </el-form-item>
                <el-form-item>
                    <el-button @click="submitEvent" type="primary" size="small">
                        提交
                    </el-button>
                </el-form-item>
            </el-form>
        </div>
    </el-card>
    <select-image :show="show_select_image" @confirm="selectImageConfirm"
                  @close="show_select_image=false"></select-image>
</div>
{hcmstag:include file="admin@/components/ueditor"}
<!--图片选择组件-->
{hcmstag:include file="admin@/components/upload/select-image"}
<script>
    $(function () {
        new Vue({
            el: ".page-container",
            data: {
                show_select_image: false,
                form: {
                    img_url: '',
                    is_menu: 1,
                    sort: 100,
                    parent_access_id: 0,
                }
            },
            mounted() {
                this.getInfo()
            },
            methods: {
                selectImageConfirm(e) {
                    console.log('selectImageConfirm', e)
                    this.form.img_url = e[0].file_url
                },
                richUpdateEvent(e) {
                    console.log('richUpdateEvent', e)
                },
                /**
                 * 获取编辑所需信息
                 */
                getInfo() {
                    // this.httpGet("{:url('admin/access/edit/info')}", {
                    //     ...this.getUrlQuery()
                    // }).then(res => {
                    // })
                },
                /**
                 * 提交信息
                 */
                submitEvent() {
                    // this.httpPost("{:url('admin/access/edit')}", {
                    //     ...this.form,
                    // }).then(res => {
                    //     if (res.status) {
                    //         this.$message.success(res.msg)
                    //     }
                    // })
                },
            }
        })
    })
</script>

<style>
</style>
