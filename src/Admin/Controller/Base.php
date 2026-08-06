<?php declare(strict_types=1);
/**
 * DuckPhp Admin System - Controller Base
 * 所有后台认证页面的基类
 */
namespace DuckAdmin\Admin\Controller;

use DuckPhp\Foundation\ControllerTrait;

class Base
{
    use ControllerTrait;
    
    protected $user;
    protected $menus;
    
    public function __construct()
    {
        Helper::checkInstall('install');
        Helper::AdminId(true);
        // 统一设置页眉页脚
        Helper::setViewHeadFoot('admin/header', 'admin/footer');
    }
    /**
     * 加载菜单配置
     */
    protected function loadMenus(): array
    {
        $config = Helper::Config('app','menus',[]);
        return $config;
    }
    
    /**
     * 渲染后台页面
     */
    protected function render(string $view, array $data = []): void
    {
        Helper::Admin()->show($data, $view);
    }
}
