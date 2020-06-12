<?php

namespace app\controller;

use app\BaseController;
use app\library\Help;

class Login extends BaseController
{
    public function index()
    {
        if (Help::isLogin()) {
            return redirect('/');
        }
        return file_get_contents(public_path() . 'public/view/login.html');
    }

    public function check($password = '')
    {
        if ($password === Help::getPassword()) {
            Help::setTokenToCookie();
            return json('登录成功!');
        }
        return json('登录失败, 密码错误!', 403);
    }

}
