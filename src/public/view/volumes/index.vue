<template>
    <div class="view">
        <div class="title">数据卷管理</div>
        <div class="content">
            <div class="tools">
                <el-button type="success" size="small" @click="loadList()">刷新</el-button>
                <el-button type="success" size="small" @click="add()">添加数据卷</el-button>
            </div>
            <el-table v-loading="loading" :data="list.Volumes" border style="max-width: 100%" max-height="600">
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
                <el-table-column label="挂载点">
                    <template slot-scope="scope">
                        <span>{{scope.row.Mountpoint}}</span>
                    </template>
                </el-table-column>
                <el-table-column label="范围(Scope)">
                    <template slot-scope="scope">
                        <span>{{scope.row.Scope}}</span>
                    </template>
                </el-table-column>
                <el-table-column label="创建时间">
                    <template slot-scope="scope">
                        <span>{{scope.row.CreatedAt|stringTime}}</span>
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
        <el-dialog title="数据卷详情" :value="''" :visible.sync="inspectItem">
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
                axios.get('/api/volumes').then(function (result) {
                    self.list = result.data;
                    self.loading = false;
                });
            },
            inspect: function (item) {
                axios.get('/api/volumes/' + item.Name)
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
                self.$prompt('请输入数据卷的名称', '提示', options)
                    .then(({value}) => {
                        var params = {
                            Name: value,
                            Driver: 'local'
                        };
                        return axios.post('/api/volumes/create', params)
                    })
                    .then(result => {
                        self.$message.success('数据卷创建成功.');
                        console.log(result.data);
                        this.loadList();
                    });
            },
            remove: function (item) {
                var self = this;

                self.$confirm('确定要删除这个数据卷吗?', '提示', {
                    confirmButtonText: '确定',
                    cancelButtonText: '取消',
                    type: 'error'
                }).then(() => {
                    axios.delete('/api/volumes/' + item.Name)
                        .then(function (result) {
                            self.loadList();
                            console.log('success: ', result.data);
                        })
                        .catch(function (result) {
                            self.loadList();
                            console.log('error: ', result.data);
                            self.$message.error('删除失败: ' + result.response.data.message);
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