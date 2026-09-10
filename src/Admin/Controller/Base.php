<?php declare(strict_types=1);
/**
 * DuckPhp Admin System - Controller Base
 * 所有后台认证页面的基类
 */
namespace DuckAdmin\Admin\Controller;

use DuckAdmin\Admin\Model\PermissionModel;
use DuckPhp\Foundation\Controller\AdminControllerBase;

class Base extends AdminControllerBase
{
    /**
     * 加载菜单(按当前管理员权限过滤,查 admin_permissions 组树)
     */
    protected function loadMenus(): array
    {
        $admin_id = (int)Helper::AdminId(false);
        return PermissionModel::_()->getUserMenus($admin_id);
    }
    
    /**
     * 渲染后台页面
     */
    protected function render(string $view, array $data = []): void
    {
        Helper::Show($data, $view);
    }
}
