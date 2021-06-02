<?php
namespace DuckAdmin\Business;

use DuckAdmin\Model\AdminModel;
use DuckAdmin\Model\RoleModel;

class AdminBusiness extends Base
{
    public function login(array $data)
    {
        $admin = AdminModel::G()->login($data['username'], $data['password']);
        BusinessException::ThrowOn(empty($admin), "用户名或密码不正确，无法登录");
        // 这里不暴露更多情况。和用户登录的严谨性不同
        return $admin;
    }
    public function getAdminList($page = 1, $page_size = 10)
    {
        $ret = AdminModel::G()->getList($page, $page_size);
        return $ret;
    }
    public function getAdmin($id)
    {
        $ret = AdminModel::G()->get($id);
        BusinessException::ThrowOn(!$ret, '没有这个管理员');
        $ret['role'] = RoleModel::G()->getRoleName($ret['role_id']); // 扩大职位详情
        return $ret;
    }

    ///////////////////
    public function addAdmin($data)
    {
        // 添加管理员
        $ret = AdminModel::G()->addData($data);
        return $ret;
    }
    public function updateAdmin($data)
    {
        //更新管理员
        if (empty($data['password'])) {
            // 更新密码
        }
        $ret = AdminModel::G()->updateData($data['id'], $data);
        return $ret;
    }
    
    public function getRoles()
    {
        return RoleModel::G()->getRoles();
    }
    ////////////
    public function checkPermission($admin, $path_info)
    {
        $admin=is_array($admin)? $admin: AdminModel::G()->get($admin);
        ServiceException::ThrowOn(!$admin, '没有这个管理员');
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
        // 把所有菜单搞到手，然后 
        $admin=is_array($admin)? $admin: AdminModel::G()->get($admin);
        
        // 我们还要判断现在是在哪个菜单里 ,让其高亮显示
        
        return [];
    }
}