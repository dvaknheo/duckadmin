<?php declare(strict_types=1);
/**
 * DuckPhp Admin System - Main Controller
 * 处理首页路由 /
 */
namespace DuckAdmin\Admin\Controller;

use DuckAdmin\Admin\Business\InstallBusiness;

class MainController extends Base
{
    public function index()
    {
        $this->render('admin/dashboard');
    }
}
