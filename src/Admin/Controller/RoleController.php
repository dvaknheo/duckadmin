<?php declare(strict_types=1);
/**
 * DuckPhp Admin System - Role Controller
 * @menu_group 系统管理
 */
namespace DuckAdmin\Admin\Controller;

use DuckAdmin\Admin\Business\RoleBusiness;
use DuckAdmin\Admin\Business\PermissionBusiness;

/**
 * @menu_name 职位管理
 */
class RoleController extends Base
{
    /** @menu 职位管理 */
    public function index()
    {
        $page = max(1, (int)(Helper::GET('page', '1')));
        $search = Helper::GET('search', '');
        $pageSize = 15;

        $data = RoleBusiness::_()->getList($page, $pageSize, $search);
        $data['page'] = $page;
        $data['pageSize'] = $pageSize;
        $data['search'] = $search;
        $data['title'] = '职位管理';
        $data['current_route'] = 'role';

        $data['urls'] = [
            'list' => __url('Role/index'),
            'create' => __url('Role/create'),
            'edit' => __url('Role/edit'),
            'delete' => __url('Role/delete'),
            'permissions' => __url('Role/permissions'),
        ];

        Helper::Show($data, 'Role/role_list');
    }

    /** @action 新增职位 */
    public function create()
    {
        $data['title'] = '新增职位';
        $data['current_route'] = 'role';
        $data['roles'] = RoleBusiness::_()->getAll();
        $data['urls'] = [
            'save' => __url('Role/save'),
            'list' => __url('Role/index'),
        ];
        $data['is_edit'] = false;
        Helper::Show($data, 'Role/role_form');
    }

    /** @action 保存职位 */
    public function save()
    {
        $name = Helper::POST('name', '');
        $description = Helper::POST('description', '');
        $pid = (int)Helper::POST('pid', '0');

        $result = RoleBusiness::_()->create($name, $description, $pid);
        if ($result['success']) {
            Helper::Show302(__url('Role/index'));
        } else {
            $data['error'] = $result['message'];
            $data['title'] = '新增职位';
            $data['current_route'] = 'role';
            $data['input'] = ['name' => $name, 'description' => $description, 'pid' => $pid];
            $data['roles'] = RoleBusiness::_()->getAll();
            $data['urls'] = [
                'save' => __url('Role/save'),
                'list' => __url('Role/index'),
            ];
            $data['is_edit'] = false;
            Helper::Show($data, 'Role/role_form');
        }
    }

    /** @action 编辑职位 */
    public function edit()
    {
        $id = (int)Helper::GET('id', '0');
        $role = RoleBusiness::_()->getById($id);
        if (!$role) {
            Helper::Show302(__url('Role/index'));
            return;
        }

        $data['role'] = $role;
        $data['title'] = '编辑职位';
        $data['current_route'] = 'role';
        $data['roles'] = RoleBusiness::_()->getAll();
        $data['urls'] = [
            'update' => __url('Role/update'),
            'list' => __url('Role/index'),
        ];
        $data['is_edit'] = true;
        Helper::Show($data, 'Role/role_form');
    }

    /** @action 更新职位 */
    public function update()
    {
        $id = (int)Helper::POST('id', '0');
        $name = Helper::POST('name', '');
        $description = Helper::POST('description', '');
        $pid = (int)Helper::POST('pid', '0');

        $result = RoleBusiness::_()->update($id, $name, $description, $pid);
        if ($result['success']) {
            Helper::Show302(__url('Role/index'));
        } else {
            $data['error'] = $result['message'];
            $data['role'] = ['id' => $id, 'name' => $name, 'description' => $description, 'pid' => $pid];
            $data['title'] = '编辑职位';
            $data['current_route'] = 'role';
            $data['roles'] = RoleBusiness::_()->getAll();
            $data['urls'] = [
                'update' => __url('Role/update'),
                'list' => __url('Role/index'),
            ];
            $data['is_edit'] = true;
            Helper::Show($data, 'Role/role_form');
        }
    }

    /** @action 删除职位 */
    public function delete()
    {
        $id = (int)Helper::GET('id', '0');
        RoleBusiness::_()->delete($id);
        Helper::Show302(__url('Role/index'));
    }

    /** @action 分配权限 */
    public function permissions()
    {
        $id = (int)Helper::GET('id', '0');

        // 处理提交
        if (Helper::SERVER('REQUEST_METHOD', '') === 'POST') {
            $permissionIds = Helper::POST('permission_ids', []);
            $permissionIds = is_array($permissionIds) ? $permissionIds : [];
            RoleBusiness::_()->setPermissions($id, $permissionIds);
            Helper::Show302(__url('Role/index'));
        }

        $role = RoleBusiness::_()->getById($id);
        if (!$role) {
            Helper::Show302(__url('Role/index'));
            return;
        }

        // 获取可分配的权限树（超管：全部权限）
        $permissions = PermissionBusiness::_()->getAll();
        $data['permissions'] = $permissions;
        $data['role'] = $role;
        $data['role_permission_ids'] = RoleBusiness::_()->getRolePermissions($id);
        $data['title'] = '职位权限分配';
        $data['current_route'] = 'role';
        $data['urls'] = [
            'save' => __url('Role/permissions'),
            'list' => __url('Role/index'),
        ];

        Helper::Show($data, 'Role/role_permissions');
    }
}
