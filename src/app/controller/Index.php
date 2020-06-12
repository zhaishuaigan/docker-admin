<?php

namespace app\controller;

use app\BaseController;
use app\library\Help;

class Index extends BaseController
{
    public function index()
    {
        if (Help::hasInstallLock() === false) {
            return redirect('/install');
        }
        if (Help::isLogin()) {
            return file_get_contents(public_path() . 'public/view/index.html');
        }
        return redirect('/login');
    }

}
