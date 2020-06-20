(function () {

    //  REQUEST 请求异常拦截
    axios.interceptors.request.use(config => {
        //==========  所有请求之前都要执行的操作  ==============
        return config;
    }, err => {
        //==================  错误处理  ====================
        return Promise.resolve(err);
    });

    //  RESPONSE 响应异常拦截
    axios.interceptors.response.use(data => {
        //==============  所有请求完成后都要执行的操作  ==================
        console.log('api success: ', data);
        return data;
    }, err => {
        console.log('api error: ', err);
        if (err && err.response) {
            switch (err.response.status) {
                case 400:
                    err.message = '请求错误(400)';
                    break;
                case 401:
                    err.message = '未授权，请重新登录(401)';
                    location.reload();
                    break;
                case 403:
                    err.message = '拒绝访问(403)';
                    break;
                case 404:
                    err.message = '请求出错(404)';
                    break;
                case 408:
                    err.message = '请求超时(408)';
                    break;
                case 500:
                    err.message = '服务器错误(500)';
                    break;
                case 501:
                    err.message = '服务未实现(501)';
                    break;
                case 502:
                    err.message = '网络错误(502)';
                    break;
                case 503:
                    err.message = '服务不可用(503)';
                    break;
                case 504:
                    err.message = '网络超时(504)';
                    break;
                case 505:
                    err.message = 'HTTP版本不受支持(505)';
                    break;
                default:
                    err.message = `连接出错(${err.response.status})!`;
            }
        } else {
            err.message = '连接服务器失败!'
        }
        return Promise.resolve(err);
    });


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
        var params = params ? params : {}
        return axios.get('/api/containers/json', {params: params});
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
    api.containers.create = function (params) {
        return axios.post('/api/containers/create', params);
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

    api.images.inspect = function (name) {
        return axios.get('/api/images/' + name + '/json');
    };

    api.images.search = function (params) {
        params = params ? params : {};
        return axios.get('/api/images/search', {params: params});
    };
    api.images.create = function (params) {
        params = params ? params : {};
        return axios.post('/api/images/create', params);
    };
    api.images.remove = function (id, params) {
        return axios.delete('/api/images/' + id + '?' + api.buildQuery(params));
    };

    api.images.prune = function () {
        return axios.post('/api/images/prune');
    };


    api.build = {};
    api.build.prune = function (params) {
        params = params ? params : {};
        return axios.post('/api/build/prune', params);
    };


    window.api = api;
})();