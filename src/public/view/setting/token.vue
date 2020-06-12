<template>
    <div class="view">
        <div class="title">设置Api Token</div>
        <div class="content">
            <el-form :inline="true" :model="form" :rules="rules" ref="form"
                     @submit.native.prevent
                     label-width="80px"
                     class="form">
                <el-form-item label="Token" prop="token">
                    <el-input v-model="form.token" required placeholder="请输入Token" style=" width: 300px;"></el-input>
                </el-form-item>
                <el-form-item>
                    <el-button type="primary" native-type="submit()" @click="submit()">确定
                    </el-button>
                </el-form-item>
            </el-form>
        </div>
    </div>
</template>

<script>
    module.exports = {
        data: function () {
            var self = this;
            return {
                form: {
                    token: '',
                },
                rules: {
                    token: [
                        {
                            validator: function (rule, value, callback) {
                                console.log(rule);
                                if (value === '') {
                                    callback(new Error('请输入Token'));
                                }
                                callback();
                            },
                            trigger: 'blur'
                        }
                    ],
                },
            };
        },
        created: function () {
            this.loadToken()
        },
        methods: {
            loadToken: function () {
                axios.get('/setting/token')
                    .then(result => {
                        this.form.token = result.data;
                    })
            },
            submit: function () {
                var self = this;
                self.$refs['form'].validate((valid) => {
                    if (!valid) {
                        this.$message.error('提交出错, 请根据提示修改资料');
                        return;
                    }

                    axios.post('/setting/token', self.form)
                        .then(result => {
                            self.$message.success(result.data);
                        })
                        .catch(result => {
                            self.$message.error(result.response.data);
                        })
                });
            }
        }

    }
</script>

<style scoped>
    .content {
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    .form {
        padding-top: 50px;
    }
</style>