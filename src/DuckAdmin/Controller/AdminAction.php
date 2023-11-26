<?php declare(strict_types=1);
/**
 * DuckPhp
 * From this time, you never be alone~
 */

namespace DuckAdmin\Controller;

use DuckPhp\Helper\ControllerHelperTrait;

use DuckAdmin\Business\AccountBusiness;
use DuckAdmin\Controller\AdminSession;

class AdminAction
{
    use ControllerHelperTrait;
    
    public function checkLogin()
    {
        $admin = $this->refresh_admin_session();
        ControllerExeption::ThrowOn(!$admin,"需要登录");
		return $admin;
    }
	/**
	 * 当前管理员
	 * @param null|array|string $fields
	 * @return array|mixed|null
	 */
	public function getCurrentAdmin()
	{
        return AdminSession::_()->getCurrentAdmin();
	}
    public function setCurrentAdmin($admin)
    {
        return AdminSession::_()->setCurrentAdmin($admin);
    }
    
	/**
	 * 刷新当前管理员session
	 * @param bool $force
	 * @return void
	 */
	protected function refresh_admin_session(bool $force = false)
	{
		$time_now = time();
		$session_ttl = 2;
		$admin = AdminSession::_()->getCurrentAdmin();
		
		if(!$admin){
			return null;
		}
		$session_last_update_time = $admin['session_last_update_time'] ?? 0;
		
		if (!$force && $time_now - $session_last_update_time < $session_ttl) {
			return null;
		}
		$admin = AccountBusiness::_()->getAdmin($admin['id']);
		if(!$admin){
			return null;
		}
		
		unset($admin['password']);
		$admin['session_last_update_time'] = $time_now;
		AdminSession::_()->setCurrentAdmin($admin);	
	}
    ////////////////
    public function checkAccess($controller,$action)
    {
        $code = 0;
        $msg = '';
		$admin = $this->getCurrentAdmin();
        try{
            AccountBusiness::_()->canAccess($admin, $controller, $action);
        } catch(\Exception $ex) {
            $this->onAuthException($ex);
            return;
        }
        $flag = $this->isOptionsMethod();
        if($flag){
            return Helper::Exit('');
        }
        return;
    }
    protected function onAuthException($ex)
    {
        $code = $ex->getCode();
        $msg = $ex->getMessage();
        
        if (Helper::IsAjax()) {
			Helper::ExitJson(['code' => $code, 'msg' => $msg, 'type' => 'error']);
		}
		if($code == 401){
			return $this->exit401();
		}else if($code == 403){
			return $this->exit403();
		}
    }
	protected function isOptionsMethod()
	{
		return Helper::SERVER('REQUEST_METHOD','GET')==='OPTIONS'?true:false;
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
		Helper::header('',true,401);
		echo $response;
		Helper::exit();
	}
	protected function exit403()
	{
		Helper::header('',true,403);
		Helper::Show('_sys/403');
		Helper::exit();
	}
    public function doShowCaptcha()
    {
        return CaptchaAction::_()->init([
            'set_phrase_handler'=>[AdminSession::_(),'setPhrase'],
            'get_phrase_handler'=>[AdminSession::_(),'getPhrase'],
        ])->doShowCaptcha();
    }
    public function doCheckCaptcha($captcha)
    {
        return CaptchaAction::_()->init([
            'set_phrase_handler'=>[AdminSession::_(),'setPhrase'],
            'get_phrase_handler'=>[AdminSession::_(),'getPhrase'],
        ])->doCheckCaptcha($captcha);
    }
}