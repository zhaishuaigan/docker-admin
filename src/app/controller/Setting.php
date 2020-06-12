<?php

namespace app\controller;

use app\BaseController;
use app\library\Help;

class Setting extends BaseController
{
    public function password($password1 = '', $password2 = '', $password3 = '')
    {
        if (!Help::isLogin()) {
            return json('没有登录!', 403);
        }
        if (Help::getPassword() !== $password1) {
            return json('旧密码错误!', 403);
        }

        if (!$password2) {
            return json('新密码不能为空!', 403);
        }

        Help::setPassword($password2);
        return json('设置成功, 请重新登录系统.');
    }

    public function token($token = '')
    {
        if ($this->request->method() === 'GET') {
            return json(Help::getApiToken());
        }
        Help::setApiToken($token);
        return json('设置成功!');

    }

}
