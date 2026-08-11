<?php declare(strict_types=1);
/**
 * DuckPhp Admin System - Role Controller
 * @menu_group 系统管理
 */
namespace DuckAdmin\Admin\Controller;

use DuckAdmin\Admin\Business\RoleBusiness;
use DuckAdmin\Admin\Business\PermissionBusiness;

/**
 * @menu_name 权限分配
 */
class RoleController extends Base
{
    /**
     * @action 角色列表
     */
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
        
        $this->render('admin/role_list', $data);
    }
    
    /**
     * 创建角色表单
     */
    /** @action 新增职位 */
    public function create()
    {
        $admin_id = (int)Session::_()->getUserId();
        $data['title'] = '新增职位';
        $data['current_route'] = 'role';
        $data['roles'] = RoleBusiness::_()->getAllManageable($admin_id);
        $this->render('admin/role_form', $data);
    }
    
    /**
     * 保存新角色
     */
    /** @action 保存职位 */
    public function save()
    {
        $admin_id = (int)Session::_()->getUserId();
        $name = Helper::POST('name', '');
        $description = Helper::POST('description', '');
        $pid = (int)Helper::POST('pid', '0');
        
        $result = RoleBusiness::_()->create($admin_id, $name, $description, $pid);
        if ($result['success']) {
            Helper::Show302(__url('role/index'));
        } else {
            $data['error'] = $result['message'];
            $data['title'] = '新增职位';
            $data['current_route'] = 'role';
            $data['input'] = ['name' => $name, 'description' => $description, 'pid' => $pid];
            $data['roles'] = RoleBusiness::_()->getAllManageable($admin_id);
            $this->render('admin/role_form', $data);
        }
    }
    
    /**
     * 编辑角色表单
     */
    /** @action 编辑职位 */
    public function edit()
    {
        $id = (int)Helper::GET('id', '0');
        $role = RoleBusiness::_()->getById($id);
        if (!$role) {
            Helper::Show302(__url('role/index'));
            return;
        }
        
        $data['role'] = $role;
        $data['title'] = '编辑职位';
        $data['current_route'] = 'role';
        $admin_id = (int)Session::_()->getUserId();
        $data['roles'] = RoleBusiness::_()->getAllManageable($admin_id);
        $this->render('admin/role_form', $data);
    }
    
    /**
     * 更新角色
     */
    /** @action 更新职位 */
    public function update()
    {
        $admin_id = (int)Session::_()->getUserId();
        $id = (int)Helper::POST('id', '0');
        $name = Helper::POST('name', '');
        $description = Helper::POST('description', '');
        $pid = (int)Helper::POST('pid', '0');
        
        $result = RoleBusiness::_()->update($admin_id, $id, $name, $description, $pid);
        if ($result['success']) {
            Helper::Show302(__url('role/index'));
        } else {
            $data['error'] = $result['message'];
            $data['role'] = ['id' => $id, 'name' => $name, 'description' => $description, 'pid' => $pid];
            $data['title'] = '编辑职位';
            $data['current_route'] = 'role';
            $data['roles'] = RoleBusiness::_()->getAllManageable($admin_id);
            $this->render('admin/role_form', $data);
        }
    }
    
    /**
     * 删除角色
     */
    /** @action 删除职位 */
    public function delete()
    {
        $id = (int)Helper::GET('id', '0');
        RoleBusiness::_()->delete($id);
        Helper::Show302(__url('role/index'));
    }
    
    /**
     * 权限分配页面
     */
    /** @action 分配权限 */
    public function permissions()
    {
        $admin_id = (int)Session::_()->getUserId();
        $id = (int)Helper::GET('id', '0');
        if ($id === 0) {
            // 未指定职位:显示可管理职位选择页
            $data['roles'] = RoleBusiness::_()->getAllManageable($admin_id);
            $data['title'] = '分配权限';
            $data['current_route'] = 'role';
            $this->render('admin/role_permissions_select', $data);
            return;
        }
        if (!RoleBusiness::_()->canManageRole($admin_id, $id)) {
            Helper::Show302(__url('role/index'));
            return;
        }
        $role = RoleBusiness::_()->getById($id);
        if (!$role) {
            Helper::Show302(__url('role/index'));
            return;
        }
        
        // 处理提交
        if (Helper::SERVER('REQUEST_METHOD', '') === 'POST') {
            $permissionIds = Helper::POST('permission_ids', []);
            $permissionIds = is_array($permissionIds) ? $permissionIds : [];
            RoleBusiness::_()->setPermissions($admin_id, $id, $permissionIds);
            Helper::Show302(__url('role/index'));
        }
        
        // 可分配权限树 = 目标职位的上级职位拥有的权限(上级=根/超管 → 全部权限树)
        $parent_id = (int)($role['pid'] ?? 0);
        $parent = $parent_id ? RoleBusiness::_()->getById($parent_id) : null;
        if (!$parent || (int)($parent['is_super'] ?? 0) === 1) {
            // 上级是根/超管:忽略一切限制,显示所有权限树
            $permissions = PermissionBusiness::_()->getAll();
        } else {
            $allowed_ids = RoleBusiness::_()->getRolePermissions($parent_id);
            $all = PermissionBusiness::_()->getAll();
            $permissions = array_values(array_filter($all, function ($p) use ($allowed_ids) {
                return in_array((int)$p['id'], $allowed_ids, true);
            }));
        }
        $data['permissions'] = $permissions;
        $data['role'] = $role;
        $data['role_permission_ids'] = RoleBusiness::_()->getRolePermissions($id);
        $data['title'] = '职位权限分配';
        $data['current_route'] = 'role';
        
        $this->render('admin/role_permissions', $data);
    }
}


