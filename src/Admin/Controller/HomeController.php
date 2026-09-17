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
        //个人信息页眉
    }
    /**
     * @menu 查看权限
     */
    public function permissions()
    {
        // 查看我的权限
    }
    /**
     * @menu 查看菜单
     */
    public function menu()
    {
        // 预览菜单
    }
}
