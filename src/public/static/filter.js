Vue.filter('time', function (value) {
    return moment.unix(value).fromNow();
});

Vue.filter('fileSize', function (value) {
    if (null === value || value === '') {
        return "0B";
    }
    var unit = ["B", "KB", "MB", "GB", "TB", "PB", "EB", "ZB", "YB"];
    var index = Math.floor(Math.log(value) / Math.log(1024));
    var size = value / Math.pow(1024, index);
    //  保留的小数位数
    size = size.toFixed(2);
    return size + unit[index];
});