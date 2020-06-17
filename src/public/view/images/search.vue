<template>
    <div class="view">
        <div class="title">搜索镜像</div>
        <div class="content">

            <el-form :inline="true" @submit.native.prevent class="form">
                <el-form-item label="关键词:">
                    <el-input v-model="form.term" placeholder="请输入要搜索的镜像名称"></el-input>
                </el-form-item>
                <el-form-item>
                    <el-button type="primary" native-type="submit()" @click="search()">搜索</el-button>
                </el-form-item>
            </el-form>
            <el-form :inline="true" @submit.native.prevent class="form" size="mini">
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
                        <a class="el-button el-button--text"
                           target="_blank"
                           v-if="scope.row.is_official"
                           :href="'https://hub.docker.com/_/' + scope.row.name">
                            {{scope.row.name}}
                        </a>
                        <a class="el-button el-button--text"
                           target="_blank"
                           v-if="!scope.row.is_official"
                           :href="'https://hub.docker.com/repository/docker/' + scope.row.name">
                            {{scope.row.name}}
                        </a>
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
                                @click.native.prevent="pull(scope.row)"
                                type="text"
                                size="small">
                            拉取镜像
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
            pull: function (item) {
                var self = this;

                var options = {
                    confirmButtonText: '确定',
                    cancelButtonText: '取消',
                    inputPattern: /\w+/,
                    inputErrorMessage: '标签格式不正确',
                    inputValue: 'latest'
                };
                self.$prompt('请输入要拉取的标签', '提示', options)
                    .then(({value}) => {
                        var params = {
                            fromImage: item.name,
                            tag: value
                        };
                        params = api.buildQuery(params);
                        self.$message.success('拉取任务创建成功, 稍后可在本地镜像列表查看.');
                        return axios.post('/api/images/create?' + params)
                    })
                    .then(result => {
                        self.$message.success('镜像 [' + item.name + '] 拉取成功.');
                        console.log(result.data);
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