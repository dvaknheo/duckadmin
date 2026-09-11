<?php declare(strict_types=1);
/**
 * DuckPhp Admin System - Permission Controller
 * @menu_group 系统管理
 */
namespace DuckAdmin\Admin\Controller;

use DuckAdmin\Admin\Business\RoleBusiness;
use DuckAdmin\Admin\Business\MenuBusiness;

/**
 * @menu_name 权限管理
 */
class PermissionController extends Base
{
    /** @menu 权限管理 */
    public function index()
    {
        $id = (int)Helper::GET('id', '0');

        // 处理提交
        if (Helper::SERVER('REQUEST_METHOD', '') === 'POST') {
            $permissionIds = Helper::POST('permission_ids', []);
            $permissionIds = is_array($permissionIds) ? $permissionIds : [];
            RoleBusiness::_()->setPermissions($id, $permissionIds);
            Helper::Show302(__url('Permission/index'));
        }

        $role = RoleBusiness::_()->getById($id);
        if (!$role) {
            Helper::Show302(__url('Role/index'));
            return;
        }

        // 获取可分配的权限树（超管：全部权限）
        $permissions = MenuBusiness::_()->getAll();
        $data['permissions'] = $permissions;
        $data['role'] = $role;
        $data['role_permission_ids'] = RoleBusiness::_()->getRolePermissions($id);
        $data['title'] = '权限分配 - ' . ($role['name'] ?? '');
        $data['current_route'] = 'permission';
        $data['urls'] = [
            'save' => __url('Permission/index'),
            'list' => __url('Permission/index'),
        ];

        Helper::Show($data, 'Permission/permission_index');
    }
}
