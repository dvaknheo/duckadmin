<?php declare(strict_types=1);
/**
 * DuckPhp Admin System - Permission Controller
 */
namespace DuckAdmin\Admin\Controller;

use DuckAdmin\Admin\Business\PermissionBusiness;

class PermissionController extends Base
{
    /**
     * 权限列表
     */
    public function index()
    {
        $page = max(1, (int)(Helper::GET('page', '1')));
        $search = Helper::GET('search', '');
        $pageSize = 15;
        
        $data = PermissionBusiness::_()->getList($page, $pageSize, $search);
        $data['page'] = $page;
        $data['pageSize'] = $pageSize;
        $data['search'] = $search;
        $data['title'] = '权限管理';
        $data['current_route'] = 'permission';
        
        $this->render('admin/permission_list', $data);
    }
    
    /**
     * 创建权限表单
     */
    public function create()
    {
        $data['title'] = '创建权限';
        $data['current_route'] = 'permission';
        $data['permissions'] = PermissionBusiness::_()->getAll();
        $this->render('admin/permission_form', $data);
    }
    
    /**
     * 保存新权限
     */
    public function save()
    {
        $input = [
            'name' => Helper::POST('name', ''),
            'url' => Helper::POST('url', ''),
            'type' => (int)Helper::POST('type', '1'),
            'parent_id' => (int)Helper::POST('parent_id', '0'),
            'weight' => (int)Helper::POST('weight', '0'),
        ];
        
        $result = PermissionBusiness::_()->create($input);
        if ($result['success']) {
            Helper::Show302(__url('permission/index'));
        } else {
            $data['error'] = $result['message'];
            $data['title'] = '创建权限';
            $data['current_route'] = 'permission';
            $data['input'] = $input;
            $data['permissions'] = PermissionBusiness::_()->getAll();
            $this->render('admin/permission_form', $data);
        }
    }
    
    /**
     * 编辑权限表单
     */
    public function edit()
    {
        $id = (int)Helper::GET('id', '0');
        $perm = PermissionBusiness::_()->getById($id);
        if (!$perm) {
            Helper::Show302(__url('permission/index'));
            return;
        }
        
        $data['perm'] = $perm;
        $data['title'] = '编辑权限';
        $data['current_route'] = 'permission';
        $data['permissions'] = PermissionBusiness::_()->getAll();
        $this->render('admin/permission_form', $data);
    }
    
    /**
     * 更新权限
     */
    public function update()
    {
        $id = (int)Helper::POST('id', '0');
        $input = [
            'name' => Helper::POST('name', ''),
            'url' => Helper::POST('url', ''),
            'type' => (int)Helper::POST('type', '1'),
            'parent_id' => (int)Helper::POST('parent_id', '0'),
            'weight' => (int)Helper::POST('weight', '0'),
        ];
        
        $result = PermissionBusiness::_()->update($id, $input);
        if ($result['success']) {
            Helper::Show302(__url('permission/index'));
        } else {
            $data['error'] = $result['message'];
            $data['perm'] = $input + ['id' => $id];
            $data['title'] = '编辑权限';
            $data['current_route'] = 'permission';
            $data['permissions'] = PermissionBusiness::_()->getAll();
            $this->render('admin/permission_form', $data);
        }
    }
    
    /**
     * 删除权限
     */
    public function delete()
    {
        $id = (int)Helper::GET('id', '0');
        PermissionBusiness::_()->delete($id);
        Helper::Show302(__url('permission/index'));
    }
}
