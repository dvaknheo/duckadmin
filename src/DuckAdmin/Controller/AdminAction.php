<?php declare(strict_types=1);
/**
 * DuckPhp
 * From this time, you never be alone~
 */

namespace DuckAdmin\Controller;

use DuckPhp\ThrowOn\ThrowOnableTrait;
use DuckPhp\Helper\ControllerHelperTrait;
use DuckPhp\SingletonEx\SingletonExTrait;
use DuckPhp\Core\Route;

use DuckAdmin\Business\AccountBusiness;
use DuckAdmin\Controller\AdminSession;
use DuckAdmin\System\ProjectAction;

class AdminAction
{
    use SingletonExTrait;
    use ControllerHelperTrait;
    use ThrowOnableTrait;

	public static function getRouteCallingClass()
	{
		return Route::G()->getRouteCallingClass();
	}
	public function initController()
	{
		if(static::IsAjax()){
			$this->assignExceptionHandler(\Exception::class,[static::class,'OnException']);
		}
		$controller = static::getRouteCallingClass();
        $action = static::getRouteCallingMethod();
		$this->checkAccess($controller,$action);
	}
	public function getCurrentAdminId()
	{
		return AdminSession::G()->getCurrentAdminId();
	}
	/**
	 * 当前管理员
	 * @param null|array|string $fields
	 * @return array|mixed|null
	 */
	public function getCurrentAdmin()
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
		
		if(!$admin){
			return null;
		}
		$session_last_update_time = $admin['session_last_update_time'] ?? 0;
		
		if (!$force && $time_now - $session_last_update_time < $session_ttl) {
			return null;
		}
		$admin = AccountBusiness::G()->getAdmin($admin['id']);
		if(!$admin){
			return null;
		}
		
		unset($admin['password']);
		$admin['session_last_update_time'] = $time_now;
		AdminSession::G()->setCurrentAdmin($admin);	
	}
    ////////////////
	

	public static function Success($data = [],$count = null)
	{
		if(is_null($count)){
			static::ExitJson(['code' => 0, 'data' => $data, 'msg' => 'ok']);
		}else{
			static::ExitJson(['code' => 0, 'msg' => 'ok', 'count' => $count, 'data' => $data]);
		}
	}
	
	protected function isOptionsMethod()
	{
		return @$_SERVER['REQUEST_METHOD']=='OPTIONS'?true:false;
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
				return static::Exit('');
			}
			return;
		}
		
		if (static::IsAjax()) {
			static::ExitJson(['code' => $code, 'msg' => $msg, 'type' => 'error']);
		}
		if($code == 401){
			return $this->exit401();
		}else if($code == 403){
			return $this->exit403();
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
	public static function OnException($ex)
	{
		$code = $ex->getCode();
		$msg = $ex->getMessage();
		if(!$code){$code = -1;}
		return static::ExitJson(['code' => $code, 'msg' => $msg, 'type' => 'error']);
	}
}