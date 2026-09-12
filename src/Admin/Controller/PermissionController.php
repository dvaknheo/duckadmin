<?php declare(strict_types=1);
namespace DuckAdmin\Admin\Controller;

use DuckAdmin\Admin\Business\RoleBusiness;
use DuckAdmin\Admin\Business\MenuBusiness;

/**
 * DuckPhp Admin System - Permission Controller
 * @menu_group 系统管理
 * @menu_directory 权限分配 Permission/index
 * @menu_weight 30
 */
class PermissionController extends Base
{
    /**
     * @menu_item 权限分配
     * @menu_weight 10
     */
    public function index()
    {
        $id = (int)Helper::GET('id', '0');
        $role = $id > 0 ? RoleBusiness::_()->getById($id) : null;
        if ($id > 0 && !$role) {
            Helper::Show302(__url('Permission/index'));
            return;
        }
        $isSuper = $role && (int)($role['is_super'] ?? 0) === 1;

        // 处理提交(超管职位由 Business 层拒绝)
        if ($role && Helper::SERVER('REQUEST_METHOD', '') === 'POST') {
            $permissionIds = Helper::POST('permission_ids', []);
            $permissionIds = is_array($permissionIds) ? $permissionIds : [];
            try {
                RoleBusiness::_()->setPermissions($id, $permissionIds);
                Helper::Show302(__url('Permission/index') . '?id=' . $id . '&saved=1');
            } catch (\Exception $ex) {
                Helper::Show302(__url('Permission/index') . '?id=' . $id . '&error=' . urlencode($ex->getMessage()));
            }
            return;
        }

        $data['roles'] = RoleBusiness::_()->getAll();
        $data['role'] = $role;
        $data['is_super'] = $isSuper;
        if ($role) {
            // 权限树(四级: 分组→目录→菜单/操作);超管默认全选且只读
            $data['tree'] = MenuBusiness::_()->getTree('all');
            $data['role_permission_ids'] = $isSuper
                ? array_map('intval', array_column(MenuBusiness::_()->getAll(), 'id'))
                : RoleBusiness::_()->getRolePermissions($id);
        }
        $data['saved'] = Helper::GET('saved', '') === '1';
        $data['error'] = (string)Helper::GET('error', '');
        $data['title'] = '权限分配' . ($role ? ' - ' . $role['name'] : '');
        $data['urls'] = [
            'self' => __url('Permission/index'),
        ];

        Helper::Show($data, 'Permission/permission_index');
    }
}
