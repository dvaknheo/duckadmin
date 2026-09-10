<?php declare(strict_types=1);
/**
 * DuckPhp Admin System - Dashboard Controller
 */
namespace DuckAdmin\Admin\Controller;

class DashboardController extends Base
{
    /**
     * 仪表盘首页
     */
    public function index()
    {
        $data = [];
        Helper::Show($data, 'admin/dashboard');
    }
}
