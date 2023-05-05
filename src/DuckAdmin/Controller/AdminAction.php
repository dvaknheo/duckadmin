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
	function refresh_admin_session(bool $force = false) //protected
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
		
		$admin = Admin::find($admin_id); 
		
		$session = request()->session();
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
    
}