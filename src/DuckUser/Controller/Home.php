<?php declare(strict_types=1);
/**
 * DuckPHP
 * From this time, you never be alone~
 */
namespace DuckUser\Controller;

use DuckUser\Business\UserBusiness;
use DuckUser\Controller\Base as C;
use DuckUser\ControllerEx\SessionManager;

class Home extends Base
{
    public function __construct()
   {
        parent::__construct();
        
        SessionManager::G()->checkCsrf();
        C::assignExceptionHandler(SessionManager::ExceptionClass(), [static::class, 'OnSessionException']);

        $csrf_token = SessionManager::G()->csrf_token();
        $csrf_field = SessionManager::G()->csrf_field();
         
        $user = SessionManager::G()->getCurrentUser();
        $user_name = $user['username'] ?? '';

        C::setViewHeadFoot('home/inc-head','home/inc-foot');
        C::assignViewData(get_defined_vars());
    }
    public static function OnSessionException($ex = null)
    {
        if(!isset($ex)){
            C::Exit404();
            return;
        }
        $code = $ex->getCode();
        __logger()->warning(''.(get_class($ex)).'('.$ex->getCode().'): '.$ex->getMessage());
        if (SessionManager::G()->isCsrfException($ex) && __is_debug()) {
            C::exit(0);
        }
        C::ExitRouteTo('login');
    }

    public function index()
    {
        $url_logout = __url('logout');
        C::Show(get_defined_vars());
    }
    public function password()
    {
        C::Show(get_defined_vars());
    }
    //////////////////////////
    public function do_password()
    {
        $error = '';
        try {
            $uid = SessionManager::G()->getCurrentUid();
            $old_pass = C::POST('oldpassword','');
            $new_pass = C::POST('newpassword','');
            $confirm_pass = C::POST('newpassword_confirm','');
            
            if($new_pass !== $confirm_pass) {
                throw new \Exception('重复密码不一致');
            }
            UserBusiness::G()->changePassword($uid, $old_pass, $new_pass);
            $error = "密码修改完毕";            
        } catch (\Exception $ex) {
            $error = $ex->getMessage();
        }
        C::Show(get_defined_vars());
    }
}