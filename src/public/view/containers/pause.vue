<template>
    <div class="view">
        <div class="title">已暂停的容器</div>
        <div class="content">
            {{name}}
        </div>
    </div>
</template>

<script>
    module.exports = {
        data: function () {
            return {
                name: 'hello'
            };
        }
    }
</script>

<style scoped>
</style>