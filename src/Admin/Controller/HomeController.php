<?php declare(strict_types=1);
/**
 * DuckPhp Admin System - Dashboard Controller
 */
namespace DuckAdmin\Admin\Controller;
/**
 * 
 * @menu_directory 我的主页
 * @menu_weight 99

 */
class HomeController extends Base
{
    /**
     * @menu 仪表盘
     */
    public function index()
    {
        $data = [];
        Helper::Show($data, 'Home/dashboard');
    }
    /**
     * @menu 个人信息
     */
    public function profile()
    {
        $data = [];
        Helper::Show($data, 'Home/profile');
    }
    /**
     * @menu 查看权限
     */
    public function permissions()
    {
        $data = [];
        Helper::Show($data, 'Home/permissions');
    }
    /**
     * @menu 查看菜单
     */
    public function menu()
    {
        $data = [];
        Helper::Show($data, 'Home/menu');
    }
}
