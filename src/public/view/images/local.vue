<template>
    <div class="view">
        <div class="title">本地镜像</div>
        <div class="content">
            <div class="tools">
                <el-button type="success" size="small" @click="loadList()">刷新</el-button>
                <el-button type="success" size="small" @click="clearBuildCache()">清除编译缓存</el-button>
                <el-button type="success" size="small" @click="prune()">清除未使用的镜像</el-button>
            </div>
            <el-table v-loading="loading" :data="list" border style="max-width: 100%" max-height="600">
                <el-table-column fixed label="镜像id">
                    <template slot-scope="scope">
                        <el-tooltip class="item" effect="dark" content="点击复制id" placement="left">
                            <el-button type="text" class="clipboard"
                                       :data-clipboard-text="scope.row.Id">{{scope.row.Id.substring(0, 16)}}
                            </el-button>
                        </el-tooltip>

                    </template>
                </el-table-column>
                <el-table-column label="镜像名字">
                    <template slot-scope="scope">
                        <el-tooltip class="item" effect="dark" content="点击查看详情" placement="left">
                            <el-button type="text" @click="inspect(scope.row)">{{scope.row.RepoTags ?
                                scope.row.RepoTags.join(',')
                                : ''}}
                            </el-button>
                        </el-tooltip>

                    </template>
                </el-table-column>
                <!--<el-table-column label="容器个数">-->
                <!--<template slot-scope="scope">-->
                <!--<span>{{scope.row.Containers}}</span>-->
                <!--</template>-->
                <!--</el-table-column>-->
                <el-table-column label="镜像大小">
                    <template slot-scope="scope">
                        <span>{{scope.row.Size|fileSize}}</span>
                    </template>
                </el-table-column>
                <el-table-column label="创建时间">
                    <template slot-scope="scope">
                        <span>{{scope.row.Created|time}}</span>
                    </template>
                </el-table-column>
                <el-table-column label="操作">
                    <template slot-scope="scope">
                        <el-button
                                @click.native.prevent="runContainer(scope.row)"
                                type="text"
                                size="small">
                            创建容器
                        </el-button>

                        <el-dropdown trigger="click">
                            <el-button type="text" size="small">更多</el-button>
                            <el-dropdown-menu slot="dropdown">
                                <el-dropdown-item @click.native.prevent="updateImage(scope.row)">更新镜像
                                </el-dropdown-item>
                                <el-dropdown-item @click.native.prevent="removeImage(scope.row)">删除镜像
                                </el-dropdown-item>
                                <!--<el-dropdown-item @click.native.prevent="forceRemoveImage(scope.row)">-->
                                <!--<el-tooltip class="item" effect="dark" content="如果有正在使用此镜像的容器会被并一起删除, 请谨慎操作."-->
                                <!--placement="right">-->
                                <!--<span>强制删除</span>-->
                                <!--</el-tooltip>-->
                                <!--</el-dropdown-item>-->
                            </el-dropdown-menu>
                        </el-dropdown>
                    </template>
                </el-table-column>
            </el-table>
        </div>
        <el-dialog title="镜像详情" :value="''" :visible.sync="inspectImage">
            基本信息
        </el-dialog>

    </div>
</template>

<script>
    module.exports = {
        data: function () {
            return {
                loading: true,
                inspectImage: null,
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
                api.images.getList().then(function (result) {
                    self.list = result.data;
                    self.loading = false;
                });
            },
            runContainer: function (item) {
                var self = this;

                self.$message.error('创建容器功能暂未实现!');
                return;

                if (!item.RepoTags) {
                    self.$message.error('没有检测到镜像标签, 不能创建容器');
                    return;
                }
                var params = {
                    Image: item.RepoTags[0],
                    ExposedPorts: {
                        '80/tcp': {}
                    },
                    HostConfig: {
                        PortBindings: {
                            "80/tcp": [{
                                "HostPort": "8888"
                            }]
                        }
                    }
                };
                self.$message.success('正在创建容器...');
                api.containers.create(params)
                    .then(result => {
                        self.$message.success('容器创建成功');
                        self.$message.success('正在启动容器');
                        console.log('create callback: ', result);
                        return api.containers.start(result.data.Id);
                    })
                    .then(result => {
                        console.log('start callback: ', result);
                        self.$message.success('容器启动成功');

                    });
            },
            inspect: function (item) {
                api.images.inspect(item.Id)
                    .then(result => {
                        console.log(result.data);
                        this.inspectImage = result.data;
                    });
            },
            clearBuildCache: function () {
                api.build.prune()
                    .then(result => {
                        this.$message.success('清除编译缓存完成.');
                        this.loadList();
                    });
            },
            prune: function () {
                api.images.prune()
                    .then(result => {
                        this.$message.success('未使用的镜像清理完成.');
                        this.loadList();
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
                            self.$message.error('删除镜像失败, 镜像正在被容器使用, 不能被删除.');
                        });
                });
            },
            updateImage: function (item) {
                var self = this;

                if (!item.RepoTags.length) {
                    self.$message.error('此镜像没有tag, 不能更新.');
                    return;
                }
                var img = item.RepoTags[0].split(':');
                var params = {
                    fromImage: img[0],
                    tag: img[1]
                };
                params = api.buildQuery(params);
                self.$message.success('更新任务创建成功, 更新完成后会有消息提醒.');
                axios.post('/api/images/create?' + params)
                    .then(function (result) {
                        self.$message.success('镜像 [' + img.join(':') + '] 更新成功.');
                        self.loadList();
                    })
                    .catch(result => {
                        self.$message.error('镜像 [' + img.join(':') + '] 更新失败: ' + result.response.data.message);
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