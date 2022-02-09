<div class="page-container" v-cloak>
    <el-card>
        <div slot="header" class="breadcrumb">
            <el-breadcrumb separator="/">
                <el-breadcrumb-item><a href="{:url('admin/main/index')}">首页</a></el-breadcrumb-item>
                <el-breadcrumb-item><a href="{:url('admin/user/index')}">管理员列表</a></el-breadcrumb-item>
                <el-breadcrumb-item>{$title}</el-breadcrumb-item>
            </el-breadcrumb>
        </div>
        <div>
            <el-form size="small" label-width="100px">
                <el-form-item label="所属角色">
                    <div>
                        <el-cascader
                                v-model="cascader_value"
                                filterable
                                :options="role_list"
                                :props="{ checkStrictly: true,value:'role_id',label:'role_name',expandTrigger: 'hover' }"
                                clearable></el-cascader>
                    </div>
                </el-form-item>
                <el-form-item label="管理员姓名">
                    <el-input v-model="form.real_name"></el-input>
                </el-form-item>
                <el-form-item label="用户名">
                    <el-input v-model="form.username"></el-input>
                </el-form-item>
                <el-form-item label="密码">
                    <el-input v-model="form.password" placeholder="不输入则不修改，新增必须填写"></el-input>
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
                cascader_value: [],
                role_list: [],
                form: {}
            },
            mounted() {
                this.getInfo()
            },
            computed: {
                role_id() {
                    if (this.cascader_value.length > 0) {
                        return this.cascader_value[this.cascader_value.length - 1]
                    } else {
                        return 0
                    }
                }
            },
            methods: {
                /**
                 * 获取联动组件的渲染值
                 * @param role_id
                 */
                getCascaderValue(role_id) {
                    this.role_list.forEach(item => {
                        if (item.children) {
                            item.children.forEach(it => {

                                it.children.forEach(i => {
                                    if (parseInt(i.role_id) === parseInt(role_id)) {
                                        this.cascader_value = [item.role_id, it.role_id, i.role_id]
                                        return
                                    }
                                })

                                if (parseInt(it.role_id) === parseInt(role_id)) {
                                    this.cascader_value = [item.role_id, it.role_id]
                                    return
                                }
                            })

                            if (parseInt(item.role_id) === parseInt(role_id)) {
                                this.cascader_value = [item.role_id]
                                return
                            }
                        }
                    })
                },
                /**
                 * 获取编辑所需信息
                 */
                getInfo() {
                    this.httpGet("{:url('admin/user/edit/info')}", {
                        ...this.getUrlQuery()
                    }).then(res => {
                        if (res.status) {
                            let {role_list = [], admin_user = {}} = res.data
                            this.role_list = role_list
                            if (admin_user.admin_user_id) {
                                this.getCascaderValue(admin_user.role_id)
                                this.form = {
                                    ...admin_user,
                                }
                            }

                        }
                    })
                },
                /**
                 * 提交信息
                 */
                submitEvent() {
                    this.httpPost("{:url('admin/user/edit')}", {
                        ...this.form,
                        role_id: this.role_id
                    }).then(res => {
                        if (res.status) {
                            this.$message.success(res.msg)
                            location.href = "{:url('admin/user/index')}"
                        } else {
                            this.$message.error(res.msg)
                        }
                    })
                },
            }
        })
    })
</script>

<style>
</style>