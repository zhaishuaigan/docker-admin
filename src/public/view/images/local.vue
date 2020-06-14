<template>
    <div class="view">
        <div class="title">本地镜像</div>
        <div class="content">
            <div class="tools">
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
                                <el-dropdown-item @click.native.prevent="removeImage(scope.row)">删除
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
                            console.log('error: ', result.data);
                            self.$message.error('删除镜像失败, 镜像正在被容器使用, 不能被删除.');
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