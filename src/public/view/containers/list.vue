<template>
    <div class="view">
        <div class="title">{{title}}</div>
        <div class="content">
            <el-table v-loading="loading" :data="list" border style="max-width: 100%" max-height="600">
                <el-table-column fixed label="容器id">
                    <template slot-scope="scope">
                        <el-tooltip class="item" effect="dark" content="点击复制id" placement="left">
                            <el-button type="text" class="clipboard"
                                       :data-clipboard-text="scope.row.Id">{{scope.row.Id.substring(0, 16)}}
                            </el-button>
                        </el-tooltip>

                    </template>
                </el-table-column>
                <el-table-column label="容器名字">
                    <template slot-scope="scope">
                        <span>{{scope.row.Names.join(',')}}</span>
                    </template>
                </el-table-column>
                <el-table-column label="镜像名字">
                    <template slot-scope="scope">
                        <span>{{scope.row.Image}}</span>
                    </template>
                </el-table-column>
                <el-table-column label="容器IP">
                    <template slot-scope="scope">
                        <span v-for="net in scope.row.NetworkSettings.Networks">{{net.IPAddress}}</span>
                    </template>
                </el-table-column>
                <el-table-column label="容器状态">
                    <template slot-scope="scope">
                        <span>{{scope.row.Status}}</span>
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
                                v-if="scope.row.State === 'exited'"
                                @click.native.prevent="startContainer(scope.row)"
                                type="text"
                                size="small">
                            启动
                        </el-button>

                        <el-button
                                v-if="scope.row.State === 'running' || scope.row.State === 'restarting'"
                                @click.native.prevent="stopContainer(scope.row)"
                                type="text"
                                size="small">
                            停止
                        </el-button>
                        <el-button
                                v-if="scope.row.State === 'paused'"
                                @click.native.prevent="unpauseContainer(scope.row)"
                                type="text"
                                size="small">
                            恢复
                        </el-button>

                        <el-dropdown trigger="click">
                            <el-button type="text" size="small">更多</el-button>
                            <el-dropdown-menu slot="dropdown">
                                <el-dropdown-item
                                        v-if="scope.row.State === 'running'"
                                        @click.native.prevent="pauseContainer(scope.row)">暂停
                                </el-dropdown-item>
                                <el-dropdown-item
                                        v-if="scope.row.State === 'paused'"
                                        @click.native.prevent="unpauseContainer(scope.row)">恢复
                                </el-dropdown-item>
                                <el-dropdown-item @click.native.prevent="restartContainer(scope.row)">重启
                                </el-dropdown-item>
                                <el-dropdown-item @click.native.prevent="removeContainer(scope.row)">删除
                                </el-dropdown-item>
                            </el-dropdown-menu>
                        </el-dropdown>
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
                title: '',
                loading: true,
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
                var currentApi = api.containers.getAll;
                self.title = '全部容器';
                switch (location.hash) {
                    case '#view-containers-run':
                        currentApi = api.containers.getRunning;
                        self.title = '正在运行的容器';
                        break;
                    case '#view-containers-exit':
                        currentApi = api.containers.getExited;
                        self.title = '已停止的容器';
                        break;
                    case '#view-containers-pause':
                        currentApi = api.containers.getPaused;
                        self.title = '已暂停的容器';
                        break;
                }
                currentApi().then(function (result) {
                    self.list = result.data;
                    self.loading = false;
                });
            },
            startContainer: function (item) {
                var self = this;
                api.containers.start(item.Id)
                    .then(function (result) {
                        self.loadList();
                        console.log('success: ', result.data);
                    })
                    .catch(function (result) {
                        self.loadList();
                        console.log('error: ', result.data);
                    });
            },
            stopContainer: function (item) {
                var self = this;

                self.$confirm('确定要停止容器吗?', '提示', {
                    confirmButtonText: '确定',
                    cancelButtonText: '取消',
                    type: 'warning'
                }).then(() => {
                    api.containers.stop(item.Id)
                        .then(function (result) {
                            self.loadList();
                            console.log('success: ', result.data);
                        })
                        .catch(function (result) {
                            self.loadList();
                            console.log('error: ', result.data);
                        });
                }).catch(() => {
                });
            },
            pauseContainer: function (item) {
                var self = this;
                self.$confirm('确定要暂停容器吗?', '提示', {
                    confirmButtonText: '确定',
                    cancelButtonText: '取消',
                    type: 'warning'
                }).then(() => {
                    api.containers.pause(item.Id)
                        .then(function (result) {
                            self.loadList();
                        })
                        .catch(function (result) {
                            self.loadList();
                            console.log('error: ', result.data);
                        });
                });
            },
            unpauseContainer: function (item) {
                var self = this;
                api.containers.unpause(item.Id)
                    .then(function (result) {
                        self.loadList();
                    })
                    .catch(function (result) {
                        self.loadList();
                        console.log('error: ', result.data);
                    });
            },
            restartContainer: function (item) {
                var self = this;
                self.$confirm('确定要重启容器吗?', '提示', {
                    confirmButtonText: '确定',
                    cancelButtonText: '取消',
                    type: 'warning'
                }).then(() => {
                    api.containers.restart(item.Id)
                        .then(function (result) {
                            self.loadList();
                        })
                        .catch(function (result) {
                            self.loadList();
                            console.log('error: ', result.data);
                        });
                });

            },
            removeContainer: function (item) {
                var self = this;

                self.$confirm('确定要删除容器吗, 删除后不可恢复?', '提示', {
                    confirmButtonText: '确定',
                    cancelButtonText: '取消',
                    type: 'error'
                }).then(() => {
                    api.containers.remove(item.Id)
                        .then(function (result) {
                            self.loadList();
                            console.log('success: ', result.data);
                        })
                        .catch(function (result) {
                            self.loadList();
                            console.log('error: ', result.data);
                            self.$message.error('删除容器失败, 正在运行的容器不能被删除.');
                        });
                });
            },
        }
    }
</script>

<style scoped>
</style>