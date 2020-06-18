<?php

namespace app\controller;

use app\BaseController;
use app\library\Help;
use think\facade\Cache;

class Index extends BaseController
{
    public function index()
    {
        if (Help::hasInstallLock() === false) {
            return redirect('/install');
        }
        if (!Help::isLogin()) {
            return redirect('/login');
        }
        if ($this->app->isDebug()) {
            cache('view_index', null);
        }
        $html = Cache::remember('view_index', function () {
            $html = file_get_contents(public_path() . 'public/view/index.html');
            $components = Help::getComponents();
            $html = str_replace('$components', $components, $html);
            return $html;
        });
        return $html;

    }

}
