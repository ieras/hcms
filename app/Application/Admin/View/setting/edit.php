<div class="page-container" v-cloak>
    <el-card>
        <div slot="header" class="breadcrumb">
            <el-breadcrumb separator="/">
                <el-breadcrumb-item><a href="{:url('admin/setting/index')}">配置列表</a></el-breadcrumb-item>
                <el-breadcrumb-item>{$title}</el-breadcrumb-item>
            </el-breadcrumb>
        </div>
        <div>
            <el-form size="small" label-width="80px">
                <el-form-item label="Key">
                    <el-input v-model="form.setting_key" placeholder="配置key，使用 {group}_{name} 格式"></el-input>
                </el-form-item>
                <el-form-item label="描述">
                    <el-input v-model="form.setting_description" placeholder="配置描述内容"></el-input>
                </el-form-item>
                <el-form-item label="值">
                    <el-input v-model="form.setting_value" type="textarea" :row="5" style="width: 400px;"></el-input>
                </el-form-item>
                <el-form-item label="分组">
                    <el-input v-model="form.setting_group" placeholder="配置分组，一般是用模块名作为分组"></el-input>
                </el-form-item>
                <el-form-item label="类型">
                    <div>
                        <el-select v-model="form.type">
                            <el-option value="string" label="字符串"></el-option>
                            <el-option value="number" label="数字"></el-option>
                            <el-option value="json" label="json"></el-option>
                        </el-select>
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
</div>

<script>
    $(function () {
        new Vue({
            el: ".page-container",
            data: {
                form: {
                    type: "string"
                }
            },
            mounted() {
                this.getInfo()
            },
            methods: {
                /**
                 * 获取编辑所需信息
                 */
                getInfo() {
                    this.httpGet("{:url('admin/setting/edit/info')}", {
                        ...this.getUrlQuery()
                    }).then(res => {
                        if (res.status) {
                            let {setting = {}} = res.data
                            if (setting.setting_id) {
                                this.form = {
                                    ...setting
                                }
                            }
                        }
                    })
                },
                /**
                 * 提交信息
                 */
                submitEvent() {
                    this.httpPost("{:url('admin/setting/edit')}", {
                        ...this.form,
                    }).then(res => {
                        if (res.status) {
                            this.$message.success(res.msg)
                            location.href = "{:url('admin/setting/index')}"
                        }
                    })
                },
            }
        })
    })
</script>

<style>
</style>
