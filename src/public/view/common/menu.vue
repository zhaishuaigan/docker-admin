<template>
    <div class="menu">
        <div class="title">Docker Admin</div>
        <el-menu :default-openeds="['containers', 'containers-all']">
            <el-submenu index="containers">
                <template slot="title"><i class="el-icon-menu"></i>容器管理</template>
                <el-menu-item index="containers-all" @click="updateView('view-containers-all')">全部容器</el-menu-item>
                <el-menu-item index="containers-run" @click="updateView('view-containers-run')">正在运行的容器</el-menu-item>
                <el-menu-item index="containers-exit" @click="updateView('view-containers-exit')">已退出的容器</el-menu-item>
                <el-menu-item index="containers-pause" @click="updateView('view-containers-pause')">已暂停的容器
                </el-menu-item>
            </el-submenu>
            <el-submenu index="images">
                <template slot="title"><i class="el-icon-menu"></i>镜像管理</template>
                <el-menu-item index="images-local" @click="updateView('view-images-local')">本地镜像</el-menu-item>
                <el-menu-item index="images-search" @click="updateView('view-images-search')">镜像搜索</el-menu-item>
                <el-menu-item index="images-shop" @click="updateView('view-images-shop')">镜像商店</el-menu-item>
                <el-menu-item index="images-build" @click="updateView('view-images-build')">编译镜像</el-menu-item>
            </el-submenu>
            <el-submenu index="setting">
                <template slot="title"><i class="el-icon-setting"></i>设置</template>
                <el-menu-item index="setting-password" @click="updateView('view-setting-password')">密码设置</el-menu-item>
                <el-menu-item index="setting-token" @click="updateView('view-setting-token')">API Token设置</el-menu-item>
            </el-submenu>
        </el-menu>
    </div>

</template>

<script>
    module.exports = {
        data: function () {
            return {
                who: 'world'
            }
        },
        methods: {
            updateView: function (view) {
                this.$emit('update', view);
            }
        }
    }
</script>

<style scoped>
    .menu {
        height: 100%;
        display: flex;
        flex-direction: column;
    }

    .menu .title {
        height: 50px;
        line-height: 50px;
        font-size: 26px;
        text-align: center;
        border-bottom: solid 1px #e6e6e6;
        border-right: solid 1px #e6e6e6;
    }

    .menu .el-menu {
        flex: 1;
        overflow-y: auto;
    }
</style>