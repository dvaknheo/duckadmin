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
	public function canAccess($admin, string $controller, string $action, int &$code = 0, string &$msg = ''): bool
	{
		C::ThrowOn(!$admin, $msg = '请登录', 401);
        // 无控制器信息说明是函数调用，函数不属于任何控制器，鉴权操作应该在函数内部完成。
        if (!$controller) {
            return true;
        }
        // 获取控制器鉴权信息
        $class = new \ReflectionClass($controller);
        $properties = $class->getDefaultProperties();
        $noNeedLogin = $properties['noNeedLogin'] ?? [];
        $noNeedAuth = $properties['noNeedAuth'] ?? [];

        // 不需要登录
        if (in_array($action, $noNeedLogin)) {
            return true;
        }
		try{


			// 不需要鉴权
			if (in_array($action, $noNeedAuth)) {
				return true;
			}
			// 当前管理员无角色
			$roles = $admin['roles'];
			C::ThrowOn(!$roles, $msg = '无权限', 2);

			// 角色没有规则
			$rule_ids = RoleModel::G()->GetRole($roles);
			C::ThrowOn(!$rule_ids, $msg = '无权限', 2);
			
			// 超级管理员
			if (in_array('*', $rule_ids)){
				return true;
			}

			// 如果action为index，规则里有任意一个以$controller开头的权限即可
			if (strtolower($action) === 'index') {
				$rule = Rule::G()->foo1($rule_ids);
				if ($rule) {
					return true;
				}
				C::ThrowOn(!$rule, $msg = '无权限', 2);
			}
			// 查询是否有当前控制器的规则
			Rule::G()->foo2($rule_ids);
			C::ThrowOn(!$rule, $msg = '无权限', 2);
			return true;
		}catch(\Exception $ex){
			$code = $ex->getCode();
			$msg = $ex->getMessage();
			return false;
			
		}
    }
}