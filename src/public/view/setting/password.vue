<template>
    <div class="view">
        <div class="title">修改管理密码</div>
        <div class="content">
            <el-form :model="form" :rules="rules" ref="form"
                     @submit.native.prevent
                     label-width="80px"
                     class="form">
                <el-form-item label="旧密码" prop="password1">
                    <el-input type="password" v-model="form.password1" required placeholder="请输入旧密码"></el-input>
                </el-form-item>
                <el-form-item label="新密码" prop="password2">
                    <el-input type="password" v-model="form.password2" required placeholder="请输入新密码"></el-input>
                </el-form-item>
                <el-form-item label="新密码" prop="password3">
                    <el-input type="password" v-model="form.password3" required placeholder="请输入新密码"></el-input>
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
                    password1: '',
                    password2: '',
                    password3: '',
                },
                rules: {
                    password1: [
                        {
                            validator: function (rule, value, callback) {
                                console.log(rule);
                                if (value === '') {
                                    callback(new Error('请输入旧密码'));
                                }
                                callback();
                            },
                            trigger: 'blur'
                        }
                    ],
                    password2: [
                        {
                            validator: function (rule, value, callback) {
                                console.log(rule);
                                if (value === '') {
                                    callback(new Error('请输入新密码'));
                                }
                                callback();
                            },
                            trigger: 'blur'
                        }
                    ],
                    password3: [
                        {
                            validator: function (rule, value, callback) {
                                console.log(rule);
                                if (value === '') {
                                    callback(new Error('请再次输入密码'));
                                }
                                if (self.form.password2 !== value) {
                                    callback(new Error('两次输入密码不一致'));
                                }
                                callback();
                            },
                            trigger: 'blur'
                        }
                    ]
                },
            };
        },
        methods: {
            submit: function () {
                var self = this;
                self.$refs['form'].validate((valid) => {
                    if (!valid) {
                        this.$message.error('提交出错, 请根据提示修改资料');
                        return;
                    }

                    axios.post('/setting/password', self.form)
                        .then(result => {
                            self.$message.success(result.data);
                            setTimeout(() => {
                                location.href = '/login';
                            }, 1000);
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
        width: 300px;
        padding-top: 50px;
    }
</style>