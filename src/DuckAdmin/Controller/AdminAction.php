<?php declare(strict_types=1);
/**
 * DuckPhp
 * From this time, you never be alone~
 */

namespace DuckAdmin\ControllerEx;

use DuckAdmin\Business\AdminBusiness;
use DuckAdmin\ControllerEx\AdminSession;
use DuckAdmin\System\ProjectAction;

class AdminAction extends ProjectAction
{
    public function doCheckPermission()
    {
        $admin = AdminSession::G()->getCurrentAdmin();
        $path_info = static::PathInfo();
        $flag = AdminBusiness::G()->checkPermission($admin,$path_info);
        return $flag;
    }
	
    public function initViewData()
    {
        // 这两个重复调用，性能可以忽略不记。
        $admin = AdminSession::G()->getCurrentAdmin();
        $path_info = static::getPathInfo();
        
        $menu = AdminBusiness::G()->getMenu($admin['id'],$path_info);
        
        static::assignViewData('menu', $menu);
        static::assignViewData('admin', $admin);
        
        static::setViewHeadFoot('header','footer');
        // 页眉页脚，如果是其他项目引用的时候，该怎么处理的问题。
    }
	
	/**
	 * 当前管理员id
	 * @return integer|null
	 */
	function admin_id(): ?int
	{
		return AdminSession::G()->getCurrentAdminId ('admin.id');
	}
/**
 * 当前管理员
 * @param null|array|string $fields
 * @return array|mixed|null
 */
function admin($fields = null)
{
    refresh_admin_session();
    if (!$admin = session('admin')) {
        return null;
    }
    if ($fields === null) {
        return $admin;
    }
    if (is_array($fields)) {
        $results = [];
        foreach ($fields as $field) {
            $results[$field] = $admin[$field] ?? null;
        }
        return $results;
    }
    return $admin[$fields] ?? null;
}

/**
 * 刷新当前管理员session
 * @param bool $force
 * @return void
 */
function refresh_admin_session(bool $force = false)
{
    if (!$admin_id = $this->admin_id()) {
        return null;
    }
    $time_now = time();
    // session在2秒内不刷新
    $session_ttl = 2;
    $session_last_update_time = session('admin.session_last_update_time', 0);
    if (!$force && $time_now - $session_last_update_time < $session_ttl) {
        return null;
    }
    $session = request()->session();
    $admin = Admin::find($admin_id); 
    if (!$admin) {
        $session->forget('admin');
        return null;
    }
    $admin = $admin->toArray();
    unset($admin['password']);
    // 账户被禁用
    if ($admin['status'] != 0) {
        $session->forget('admin');
        return;
    }
    $admin['roles'] = AdminRole::where('admin_id', $admin_id)->pluck('role_id')->toArray();
    $admin['session_last_update_time'] = $time_now;
    $session->set('admin', $admin);
}
    public function canAccess(string $controller, string $action, int &$code = 0, string &$msg = ''): bool
    {
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
			// 获取登录信息
			$admin = AdminSession::G()->getCurrentAdmin();
			C::ThrowOn(!$admin, $msg = '请登录', 401);

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
			Rule::G()->foo2($rule_ids);
			// 查询是否有当前控制器的规则
			$rule = Rule::where(function ($query) use ($controller, $action) {
				$query->where('key', "$controller@$action")->orWhere('key', $controller);
			})->whereIn('id', $rule_ids)->first();
			C::ThrowOn(!$rule, $msg = '无权限', 2);
			return true;
		}catch(\Exception $ex){
			$code = $ex->getCode();
			$msg = $ex->getMessage();
			return false;
			
		}
    }
}