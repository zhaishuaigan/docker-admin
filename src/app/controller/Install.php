<?php

namespace app\controller;

use app\BaseController;
use app\library\Help;

class Install extends BaseController
{
    public function index()
    {
        if (Help::hasInstallLock()) {
            return redirect('/login');
        }
        return file_get_contents(public_path() . 'public/view/install.html');
    }

    public function setting($password = '')
    {
        if (Help::hasInstallLock()) {
            return json('安装程序已经执行完成, 请勿重复执行.', 403);
        }
        Help::setPassword($password);
        Help::setAuthKey(Help::makeAuthKey());
        Help::setApiToken(Help::makeApiToken());
        Help::makeInstallLock();
        return json('安装完成');
    }

}
