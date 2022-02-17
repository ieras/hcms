<div class="page-container" v-cloak>
    <el-card>
        <div slot="header" class="breadcrumb">
            <el-breadcrumb separator="/">
                <el-breadcrumb-item>示例配置</el-breadcrumb-item>
            </el-breadcrumb>
        </div>
        <div>
            <el-form size="small" label-width="120px">
                <el-form-item required label="示例文本配置">
                    <el-input v-model="setting.demo_string" placeholder="请输入示例文本配置"></el-input>
                </el-form-item>
                <el-form-item required label="示例数字配置">
                    <el-input v-model="setting.demo_number" type="number" placeholder="请输入示例数字配置"></el-input>
                </el-form-item>
                <el-form-item required label="示例Json配置">
                    <el-input v-model="setting.demo_json" placeholder="请输入示例数字配置"></el-input>
                </el-form-item>
                <el-form-item>
                    <el-button @click="submitEvent" type="primary">保存</el-button>
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
                setting: {
                    demo_string: "示例字符串",
                    demo_number: 20,
                    demo_json: '{"msg":"ok"}',
                }
            },
            mounted() {
                this.getInfo()
            },
            methods: {
                getInfo() {
                    this.httpGet("{:url('demo/demo/setting/info')}", {}).then(res => {
                        if (res.status) {
                            let {setting = {}} = res.data
                            for (let key in setting) {
                                //为了输入框显示，这里将对象转成字符串
                                if (typeof setting[key] == "object") {
                                    setting[key] = JSON.stringify(setting[key])
                                }
                            }
                            this.setting = {
                                ...setting
                            }
                        }
                    })
                },
                submitEvent() {
                    this.httpPost("{:url('demo/demo/setting')}", {setting: this.setting}).then(res => {
                        if (res.status) {
                            this.$message.success(res.msg)
                        }
                    })
                }
            }
        })
    })
</script>