<?php
namespace DuckAdmin\Service;

use DuckAdmin\Model\AdminModel;

class AdminService extends BaseService
{
    public function login(array $data)
    {
        $admin = AdminModel::G()->login($data['username'], $data['password']);
        ServiceException::ThrowOn(empty($admin), "用户名或密码不正确，无法登录");
        // 这里不暴露更多情况。和用户登录的严谨性不同
        return $admin;
    }
    public function getAdminList($page = 1, $page_size = 10)
    {
        $ret = AdminModel::G()->getList($page, $page_size);
        return $ret;
    }
    ///////////////////
    public function add($data)
    {
        $ret = AdminModel::G()->addData($data['username'],$data['nick'],$data['password']);
        return $ret;
    }
    public function checkPermission($admin, $path_info)
    {
        $admin=is_array($admin)? $admin: AdminModel::G()->get($admin);
        ServiceException::ThrowOn(!$admin, '请重新登录');
        if($admin['id']==1){
            return true;
        }
        $role=RoleModel::G()->find($admin['role']);
        if(!$role){
            return false;
        }
        if($path_info===$role['path']){
            return true;
        }
        return false;
    }
    public function getMenu($admin,$path_info='')
    {
        //$menus 
        // 把所有菜单搞到手，然后 
        $admin=is_array($admin)? $admin: AdminModel::G()->get($admin);
        
    }
}