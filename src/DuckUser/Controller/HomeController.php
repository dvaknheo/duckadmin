<?php declare(strict_types=1);
/**
 * DuckPHP
 * From this time, you never be alone~
 */
namespace DuckUser\Controller;

use DuckPhp\Component\SimpleControllerTrait;

use DuckUser\Business\UserBusiness;
use DuckUser\Controller\UserAction as Helper;

class Home
{
    use SimpleControllerTrait;
    
    public function __construct()
    {        
        Helper::G()->initController(static::class);
    }
    public static function OnSessionException($ex = null)
    {
        if(!isset($ex)){
            Helper::Exit404();
            return;
        }
        $code = $ex->getCode();
        __logger()->warning(''.(get_class($ex)).'('.$ex->getCode().'): '.$ex->getMessage());
        if (Session::G()->isCsrfException($ex) && __is_debug()) {
            Helper::exit(0);
        }
        Helper::ExitRouteTo('login');
    }

    public function index()
    {
        $url_logout = __url('logout');
        Helper::Show(get_defined_vars());
    }
    public function password()
    {
        Helper::Show(get_defined_vars());
    }
    //////////////////////////
    public function do_password()
    {
        $error = '';
        try {
            $uid = Helper::UserId();
            $old_pass = Helper::POST('oldpassword','');
            $new_pass = Helper::POST('newpassword','');
            $confirm_pass = Helper::POST('newpassword_confirm','');
            
            Helper::ThrowOn($new_pass !== $confirm_pass, '重复密码不一致');
            UserBusiness::G()->changePassword($uid, $old_pass, $new_pass);
            
            $error = "密码修改完毕"; 
        } catch (\Exception $ex) {
            $error = $ex->getMessage();
        }
        Helper::Show(get_defined_vars());
    }
}