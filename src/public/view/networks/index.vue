<template>
    <div class="view">
        <div class="title">网络管理</div>
        <div class="content">
            <div class="tools">
                <el-button type="success" size="small" @click="loadList()">刷新</el-button>
                <el-button type="success" size="small" @click="add()">添加网络</el-button>
            </div>
            <el-table v-loading="loading" :data="list" border style="max-width: 100%" max-height="600">
                <el-table-column fixed label="id">
                    <template slot-scope="scope">
                        <el-tooltip class="item" effect="dark" content="点击复制id" placement="left">
                            <el-button type="text" class="clipboard"
                                       :data-clipboard-text="scope.row.Id">{{scope.row.Id.substring(0, 16)}}
                            </el-button>
                        </el-tooltip>
                    </template>
                </el-table-column>
                <el-table-column label="名称">
                    <template slot-scope="scope">
                        <el-tooltip class="item" effect="dark" content="点击查看详情" placement="left">
                            <el-button type="text" @click="inspect(scope.row)">{{scope.row.Name}}
                            </el-button>
                        </el-tooltip>
                    </template>
                </el-table-column>
                <el-table-column label="驱动">
                    <template slot-scope="scope">
                        <span>{{scope.row.Driver}}</span>
                    </template>
                </el-table-column>
                <el-table-column label="范围(Scope)">
                    <template slot-scope="scope">
                        <span>{{scope.row.Scope}}</span>
                    </template>
                </el-table-column>
                <el-table-column label="创建时间">
                    <template slot-scope="scope">
                        <span>{{scope.row.Created|stringTime}}</span>
                    </template>
                </el-table-column>
                <el-table-column label="操作">
                    <template slot-scope="scope">
                        <el-button
                                @click.native.prevent="inspect(scope.row)"
                                type="text"
                                size="small">
                            查看详情
                        </el-button>

                        <el-dropdown trigger="click">
                            <el-button type="text" size="small">更多</el-button>
                            <el-dropdown-menu slot="dropdown">
                                <el-dropdown-item @click.native.prevent="remove(scope.row)">删除
                                </el-dropdown-item>
                            </el-dropdown-menu>
                        </el-dropdown>
                    </template>
                </el-table-column>
            </el-table>
        </div>
        <el-dialog title="网络详情" :value="''" :visible.sync="inspectItem">
            基本信息
        </el-dialog>

    </div>
</template>

<script>
    module.exports = {
        data: function () {
            return {
                loading: true,
                inspectItem: null,
                list: []
            };
        },
        created: function () {
            this.loadList();
        },
        methods: {
            loadList: function () {
                var self = this;
                self.loading = true;
                axios.get('/api/networks').then(function (result) {
                    self.list = result.data;
                    self.loading = false;
                });
            },
            inspect: function (item) {
                axios.get('/api/networks/' + item.Id)
                    .then(result => {
                        console.log(result.data);
                        this.inspectItem = result.data;
                    });
            },
            add: function () {
                var self = this;
                var options = {
                    confirmButtonText: '确定',
                    cancelButtonText: '取消',
                    inputPattern: /\w+/,
                    inputErrorMessage: '名称格式不正确',
                    inputValue: ''
                };
                self.$prompt('请输入网络名称', '提示', options)
                    .then(({value}) => {
                        var params = {
                            Name: value,
                            Driver: 'bridge'
                        };
                        return axios.post('/api/networks/create', params)
                    })
                    .then(result => {
                        self.$message.success('网络创建成功.');
                        console.log(result.data);
                        this.loadList();
                    });
            },
            remove: function (item) {
                var self = this;

                self.$confirm('确定要删除这个网络吗?', '提示', {
                    confirmButtonText: '确定',
                    cancelButtonText: '取消',
                    type: 'error'
                }).then(() => {
                    axios.delete('/api/networks/' + item.Id)
                        .then(function (result) {
                            self.loadList();
                            console.log('success: ', result.data);
                        })
                        .catch(function (result) {
                            self.loadList();
                            console.log('error: ', result.data);
                            self.$message.error('删除网络失败: ' + result.response.data.message);
                        });
                });
            },
        }
    }
</script>

<style scoped>
    .tools {
        padding-bottom: 10px;
    }
</style>