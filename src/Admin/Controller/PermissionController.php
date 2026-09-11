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

        // 无 id: 先选择职位
        if ($id <= 0) {
            $data['roles'] = RoleBusiness::_()->getAll();
            $data['title'] = '权限分配';
            $data['urls'] = [
                'assign' => __url('Permission/index'),
                'home' => __url('Home/index'),
            ];
            Helper::Show($data, 'Permission/select');
            return;
        }

        $role = RoleBusiness::_()->getById($id);
        if (!$role) {
            Helper::Show302(__url('Permission/index'));
            return;
        }
        $isSuper = (int)($role['is_super'] ?? 0) === 1;

        // 处理提交
        if (Helper::SERVER('REQUEST_METHOD', '') === 'POST') {
            $permissionIds = Helper::POST('permission_ids', []);
            $permissionIds = is_array($permissionIds) ? $permissionIds : [];
            $result = RoleBusiness::_()->setPermissions($id, $permissionIds);
            $qs = $result['success'] ? 'saved=1' : 'error=' . urlencode($result['message']);
            Helper::Show302(__url('Permission/index') . '?id=' . $id . '&' . $qs);
            return;
        }

        // 权限树(四级: 分组→目录→菜单/操作);超管默认全选且只读
        $data['tree'] = MenuBusiness::_()->getTree('all');
        $data['role'] = $role;
        $data['is_super'] = $isSuper;
        $data['role_permission_ids'] = $isSuper
            ? array_map('intval', array_column(MenuBusiness::_()->getAll(), 'id'))
            : RoleBusiness::_()->getRolePermissions($id);
        $data['saved'] = Helper::GET('saved', '') === '1';
        $data['error'] = (string)Helper::GET('error', '');
        $data['title'] = '权限分配 - ' . ($role['name'] ?? '');
        $data['urls'] = [
            'save' => __url('Permission/index'),
            'list' => __url('Permission/index'),
        ];

        Helper::Show($data, 'Permission/permission_index');
    }
}
