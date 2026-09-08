<?php declare(strict_types=1);
/**
 * DuckAdmin SingleAdmin - Home Controller
 * index() 登录后主页,带登出按钮
 */
namespace DuckAdmin\SingleAdmin\Controller;

use DuckPhp\Foundation\Controller\AdminControllerBase;
use DuckPhp\Foundation\Controller\Helper;

class HomeController extends AdminControllerBase
{
    public function index()
    {
        $data = [];
        Helper::Show($data, 'home');
    }
}
