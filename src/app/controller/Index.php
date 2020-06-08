<?php

namespace app\controller;

use app\BaseController;

class Index extends BaseController
{
    public function index()
    {

        return file_get_contents(public_path() . 'public/view/index.html');
    }

}
