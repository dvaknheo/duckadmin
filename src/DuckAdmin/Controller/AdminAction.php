<?php declare(strict_types=1);
/**
 * DuckPhp
 * From this time, you never be alone~
 */

namespace DuckAdmin\ControllerEx;

use DuckAdmin\Business\AccountBusiness;
use DuckAdmin\Controller\AdminSession;
use DuckAdmin\System\ProjectAction;

class AdminAction extends ProjectAction
{
	/**
	 * 当前管理员
	 * @param null|array|string $fields
	 * @return array|mixed|null
	 */
	public function admin($fields = null)
	{
		$this->refresh_admin_session();		
		return AdminSession::G()->getCurrentAdmin();
	}

	/**
	 * 刷新当前管理员session
	 * @param bool $force
	 * @return void
	 */
	protected function refresh_admin_session(bool $force = false) //protected
	{
		$time_now = time();
		$session_ttl = 2;
		$admin = AdminSession::G()->getCurrentAdmin();
		
		$session_last_update_time = $admin['session_last_update_time'] ?? 0;
		
		if (!$force && $time_now - $session_last_update_time < $session_ttl) {
			return null;
		}
		
		$admin = AccountBusiness::G()->getAdmin($admin_id);
		if(!$admin){
			return null;
		}
		
		unset($admin['password']);
		$admin['session_last_update_time'] = $time_now;
		AdminSession::G()->setCurrentAdmin($admin);	
	}
    ////////////////
	
	public static function json(int $code, string $msg = 'ok', array $data = []): Response
    {
        return static::ExitJson(['code' => $code, 'data' => $data, 'msg' => $msg]);
    }
	
	public static function Success($data)
	{
		return static::json(['code' => 0, 'data' => $data, 'msg' => 'ok']);
	}
	protected function isOptionsMethod()
	{
		return $_SERVER['REQUEST_METHOD']=='OPTIONS'?true:false;
	}
    public function checkAccess($controller,$action)
    {
        $code = 0;
        $msg = '';
		
		$admin = $this->getCurrentAdmin();	
		$flag = AccountBusiness::G()->canAccess($admin, $controller, $action, $code, $msg);
		if($flag){
			$flag = $this->isOptionsMethod();
			if($flag){
				echo '';
				static::Exit();
			}
		}
		
		if (static::IsJson()) {
			static::ExitJson(['code' => $code, 'msg' => $msg, 'type' => 'error']);
		}
		if($code == 401){
			$this->exit401();
		}else if($code == 403){
			$this->exit403();
		}		
    }
	protected function exit401()
	{
		$response = <<<EOF
<script>
if (self !== top) {
	parent.location.reload();
}
</script>
EOF;
		static::Header(403);
		echo $response;
		static::Exit();
	}
	protected function exit403()
	{
		static::Header(403);
		static::Show('_sys/403');
		static::Exit();
	}
}