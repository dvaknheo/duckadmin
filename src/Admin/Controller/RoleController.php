<?php declare(strict_types=1);
/**
 * DuckPhp Admin System - Role Controller
 */
namespace DuckAdmin\Admin\Controller;

use DuckAdmin\Admin\Business\RoleBusiness;
use DuckAdmin\Admin\Business\PermissionBusiness;

class RoleController extends Base
{
    /**
     * 角色列表
     */
    public function index()
    {
        $page = max(1, (int)(Helper::GET('page', '1')));
        $search = Helper::GET('search', '');
        $pageSize = 15;
        
        $data = RoleBusiness::_()->getList($page, $pageSize, $search);
        $data['page'] = $page;
        $data['pageSize'] = $pageSize;
        $data['search'] = $search;
        $data['title'] = '角色管理';
        $data['current_route'] = 'role';
        
        $this->render('admin/role_list', $data);
    }
    
    /**
     * 创建角色表单
     */
    public function create()
    {
        $data['title'] = '创建角色';
        $data['current_route'] = 'role';
        $this->render('admin/role_form', $data);
    }
    
    /**
     * 保存新角色
     */
    public function save()
    {
        $name = Helper::POST('name', '');
        $description = Helper::POST('description', '');
        
        $result = RoleBusiness::_()->create($name, $description);
        if ($result['success']) {
            Helper::Show302(__url('role/index'));
        } else {
            $data['error'] = $result['message'];
            $data['title'] = '创建角色';
            $data['current_route'] = 'role';
            $data['input'] = ['name' => $name, 'description' => $description];
            $this->render('admin/role_form', $data);
        }
    }
    
    /**
     * 编辑角色表单
     */
    public function edit()
    {
        $id = (int)Helper::GET('id', '0');
        $role = RoleBusiness::_()->getById($id);
        if (!$role) {
            Helper::Show302(__url('role/index'));
            return;
        }
        
        $data['role'] = $role;
        $data['title'] = '编辑角色';
        $data['current_route'] = 'role';
        $this->render('admin/role_form', $data);
    }
    
    /**
     * 更新角色
     */
    public function update()
    {
        $id = (int)Helper::POST('id', '0');
        $name = Helper::POST('name', '');
        $description = Helper::POST('description', '');
        
        $result = RoleBusiness::_()->update($id, $name, $description);
        if ($result['success']) {
            Helper::Show302(__url('role/index'));
        } else {
            $data['error'] = $result['message'];
            $data['role'] = ['id' => $id, 'name' => $name, 'description' => $description];
            $data['title'] = '编辑角色';
            $data['current_route'] = 'role';
            $this->render('admin/role_form', $data);
        }
    }
    
    /**
     * 删除角色
     */
    public function delete()
    {
        $id = (int)Helper::GET('id', '0');
        RoleBusiness::_()->delete($id);
        Helper::Show302(__url('role/index'));
    }
    
    /**
     * 权限分配页面
     */
    public function permissions()
    {
        $id = (int)Helper::GET('id', '0');
        $role = RoleBusiness::_()->getById($id);
        if (!$role) {
            Helper::Show302(__url('role/index'));
            return;
        }
        
        // 处理提交
        if (Helper::SERVER('REQUEST_METHOD', '') === 'POST') {
            $permissionIds = Helper::POST('permission_ids', []);
            $permissionIds = is_array($permissionIds) ? $permissionIds : [];
            RoleBusiness::_()->setPermissions($id, $permissionIds);
            Helper::Show302(__url('role/index'));
        }
        
        $data['role'] = $role;
        $data['permissions'] = PermissionBusiness::_()->getAll();
        $data['role_permission_ids'] = RoleBusiness::_()->getRolePermissions($id);
        $data['title'] = '角色权限分配';
        $data['current_route'] = 'role';
        
        $this->render('admin/role_permissions', $data);
    }
}
