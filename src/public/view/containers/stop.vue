<template>
    <div class="view">
        <div class="title">已停止的容器</div>
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
        },
        created: function () {

        },
        methods: {}
    }
</script>

<style scoped>
</style>