<template>
    <div class="view">
        <div class="title">搜索镜像</div>
        <div class="content">

            <el-form :inline="true" class="form">
                <el-form-item label="关键词:">
                    <el-input v-model="form.term" placeholder="请输入要搜索的镜像名称"></el-input>
                </el-form-item>
                <el-form-item>
                    <el-button type="primary" @click="search()">搜索</el-button>
                </el-form-item>
            </el-form>
            <el-form :inline="true" class="form" size="mini">
                <el-form-item label="返回条数">
                    <el-select v-model="form.limit" class="limit" placeholder="设置返回条数">
                        <el-option label="20条" value="20"></el-option>
                        <el-option label="50条" value="50"></el-option>
                        <el-option label="100条" value="100"></el-option>
                    </el-select>
                </el-form-item>
                <el-form-item label="Star数">
                    <el-select v-model="form.filters.stars" class="limit" placeholder="Stars数">
                        <el-option label="0+" value="0"></el-option>
                        <el-option label="100+" value="100"></el-option>
                        <el-option label="1K+" value="1000"></el-option>
                        <el-option label="10K+" value="10000"></el-option>
                    </el-select>
                </el-form-item>
                <el-form-item label="只看官方" prop="delivery">
                    <el-switch v-model="form.filters['is-official']"></el-switch>
                </el-form-item>
            </el-form>

            <el-table v-loading="loading" :data="list" border style="max-width: 100%" max-height="600">
                <el-table-column label="镜像名字">
                    <template slot-scope="scope">
                        <span @click="inspect(scope.row)">{{scope.row.name}}</span>
                    </template>
                </el-table-column>
                <el-table-column label="官方">
                    <template slot-scope="scope">
                        <span>{{scope.row.is_official? '是' : '否'}}</span>
                    </template>
                </el-table-column>
                <el-table-column label="Star数">
                    <template slot-scope="scope">
                        <span>{{scope.row.star_count}}</span>
                    </template>
                </el-table-column>
                <el-table-column label="简介">
                    <template slot-scope="scope">
                        <span>{{scope.row.description}}</span>
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
                    </template>
                </el-table-column>
            </el-table>
        </div>
    </div>
</template>

<script>
    module.exports = {
        data: function () {
            return {
                form: {
                    term: 'nginx',
                    limit: 20,
                    filters: {
                        'is-automated': false,
                        'is-official': false,
                        stars: 0
                    }
                },
                keyword: '',
                loading: false,
                list: []
            };
        },
        created: function () {
            // this.search();
        },
        methods: {
            search: function () {
                var self = this;
                self.loading = true;
                self.list = [];
                var filters = {};
                for (var i in self.form.filters) {
                    if (self.form.filters[i]) {
                        filters[i] = [self.form.filters[i].toString()];
                    }
                }
                var params = {
                    term: self.form.term,
                    limit: self.form.limit,
                    filters: JSON.stringify(filters)
                };
                api.images.search(params)
                    .then(result => {
                        self.list = result.data;
                    })
                    .catch(result => {
                        self.$message.error(result.response.data.message);
                    })
                    .finally(() => {
                        self.loading = false;
                    });
            },
            runContainer: function (item) {
            },
            inspect: function (item) {
                console.log(item);
                api.images.inspect(item.name)
                    .then(result => {
                        console.log('image:', result.data);
                    });
            },
            removeImage: function (item) {
                var self = this;

                self.$confirm('确定要删除这个镜像吗?', '提示', {
                    confirmButtonText: '确定',
                    cancelButtonText: '取消',
                    type: 'error'
                }).then(() => {
                    api.images.remove(item.Id)
                        .then(function (result) {
                            self.loadList();
                            console.log('success: ', result.data);
                        })
                        .catch(function (result) {
                            self.loadList();
                            console.log('error: ', result.data);
                            self.$message.error('删除镜像失败, 镜像正在被容器使用, 不能被删除.');
                        });
                });
            },
            forceRemoveImage: function (item) {
                var self = this;

                self.$confirm('确定要强制删除这个镜像吗? 此操作将一并删除已经停止运行的容器, 请谨慎操作.', '提示', {
                    confirmButtonText: '确定',
                    cancelButtonText: '取消',
                    type: 'error'
                }).then(() => {
                    api.images.remove(item.Id, {force: true})
                        .then(function (result) {
                            self.loadList();
                            console.log('success: ', result.data);
                        })
                        .catch(function (result) {
                            self.loadList();
                            console.log('error: ', result.data);
                            self.$message.error('删除镜像失败, 镜像正在被容器使用, 不能被删除.');
                        });
                });
            },
        }
    }
</script>

<style scoped>
    .form {
        text-align: center;
    }

    .limit {
        width: 80px;
    }
</style>