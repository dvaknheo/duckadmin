<?php
namespace plugin\admin\app\common;


use plugin\admin\app\model\Admin;
use plugin\admin\app\model\AdminRole;
use plugin\admin\app\model\Role;
use plugin\admin\app\model\Rule;

class Auth
{
    /**
     * 获取权限范围内的所有角色id
     * @param bool $with_self
     * @return array
     */
    public static function getScopeRoleIds(bool $with_self = false): array
    {
        if (!$admin = admin()) {
            return [];
        }
		
		// 这里
        $role_ids = $admin['roles'];
        $rules = Role::whereIn('id', $role_ids)->pluck('rules')->toArray();
        if ($rules && in_array('*', $rules)) {
            return Role::pluck('id')->toArray();
        }

        $roles = Role::get(); 
        $descendants = (new Tree($roles))->getDescendant($role_ids, $with_self);
        return array_column($descendants, 'id');
    }

    /**
     * 获取权限范围内的所有管理员id
     * @param bool $with_self
     * @return array
     */
    public static function getScopeAdminIds(bool $with_self = false): array
    {
        $role_ids = static::getScopeRoleIds($with_self);
        return AdminRole::whereIn('role_id', $role_ids)->pluck('admin_id')->toArray();
    }

    /**
     * 是否是超级管理员
     * @param int $admin_id
     * @return bool
     */
    
	
    public function isSupperAdmin(int $admin_id = 0): bool
    {
        if (!$admin_id) {
			$roles = admin('roles'); // TODO change
            if (!$roles) {
                return false;
            }
        } else {
            $roles = AdminRoleModel::G()->getRoles($admin_id);
        }
        
        return   Role::G()->hasSuperAdmin($roles);
    }
/**
     * 判断是否有权限
     * @param string $controller
     * @param string $action
     * @param int $code
     * @param string $msg
     * @return bool
     * @throws \ReflectionException|BusinessException
     */

}