<?php declare(strict_types=1);
/**
 * DuckPhp Admin System - Dashboard Controller
 */
namespace DuckAdmin\Admin\Controller;

class HomeController extends Base
{
    /**
     * 个人主页
     */
    public function index()
    {
        $data = [];
        Helper::Show($data, 'Home/dashboard');
    }
    public function profile()
    {
        //个人信息页眉
    }
    /**
     * @menu 我的权限
     */
    public function permissions()
    {
        // 查看我的权限
    }
    /**
     * @menu 预览菜单
     */
    public function menu()
    {
        // 预览菜单
    }
}
