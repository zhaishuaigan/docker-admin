<?php

namespace app\controller;

use app\BaseController;
use app\library\Docker;
use app\library\Help;
use think\facade\Log;

class Api extends BaseController
{
    public function index()
    {
        if (Help::isLogin() === false) {
            return json('当前处于未登录状态', 401);
        }

        $url = $this->request->url();
        $url = str_replace('/api', '', $url);
        $method = $this->request->method();
        $result = [
            'data' => null,
            'info' => [
                'http_code' => 405
            ]
        ];
        switch ($method) {
            case 'GET':
                $result = Docker::get($url);
                break;
            case 'POST':
                $params = file_get_contents('php://input');
                $result = Docker::post($url, json_decode($params));
                break;
            case 'PUT':
                $params = file_get_contents('php://input');
                $result = Docker::put($url, json_decode($params));
                break;
            case 'DELETE':
                $result = Docker::delete($url);
                break;
        }

        if (env('app_debug')) {
            Log::write(json_encode($result), 'info');
        }

        return json($result['data'], $result['info']['http_code']);
    }

    public function hello($name = 'ThinkPHP6')
    {
        return 'hello,' . $name;
    }
}
