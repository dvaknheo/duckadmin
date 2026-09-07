<?php declare(strict_types=1);
/**
 * DuckAdmin DemoUsers - Home Controller
 * index() 登录后主页,带登出按钮
 */
namespace DuckAdmin\DemoUsers\Controller;

use DuckPhp\Foundation\Controller\UserControllerBase;

class HomeController extends UserControllerBase
{
    public function index()
    {
        $data = [];
        Helper::Show($data, 'home');
    }
}
