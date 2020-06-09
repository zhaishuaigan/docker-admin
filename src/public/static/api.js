(function () {

    var api = {};
    api.buildQuery = function (params) {
        var result = [];
        params = params ? params : [];
        for (var i in params) {
            result.push(i + '=' + params[i]);
        }
        return result.join('&');
    };
    api.containers = {};
    api.containers.getList = function (params) {
        return axios.get('/api/containers/json?' + api.buildQuery(params));
    };
    api.containers.getAll = function () {
        return api.containers.getList({
            all: true
        })
    };
    api.containers.getRunning = function () {
        return api.containers.getList({
            filters: JSON.stringify({
                status: ['running']
            })
        })
    };
    api.containers.getPaused = function () {
        return api.containers.getList({
            filters: JSON.stringify({
                status: ['paused']
            })
        })
    };
    api.containers.getExited = function () {
        return api.containers.getList({
            filters: JSON.stringify({
                status: ['exited']
            })
        })
    };

    api.containers.start = function (id) {
        return axios.post('/api/containers/' + id + '/start');
    };

    api.containers.stop = function (id) {
        return axios.post('/api/containers/' + id + '/stop');
    };
    api.containers.pause = function (id) {
        return axios.post('/api/containers/' + id + '/pause');
    };
    api.containers.unpause = function (id) {
        return axios.post('/api/containers/' + id + '/unpause');
    };
    api.containers.restart = function (id) {
        return axios.post('/api/containers/' + id + '/restart');
    };
    api.containers.remove = function (id) {
        return axios.delete('/api/containers/' + id);
    };

    api.images = {};
    api.images.getList = function (params) {
        return axios.get('/api/images/json?' + api.buildQuery(params));
    };
    api.images.search = function (params) {
        return axios.get('/api/images/search?' + api.buildQuery(params));
    };


    window.api = api;
})();