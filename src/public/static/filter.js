Vue.filter('time', function (value) {
    return moment.unix(value).fromNow();
})