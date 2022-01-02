<?php declare(strict_types=1);
/**
 * DuckPHP
 * From this time, you never be alone~
 */
namespace DuckUser\Controller;

use DuckUser\Business\UserBusiness;
use DuckUser\Controller\Base as C;
use DuckUser\ControllerEx\SessionManager;

class Main
{
    protected $url_home = 'Home/index';
    public function __construct()
    {
        $this->initController();
    }
    protected function initController()
    {
        $method = C::getRouteCallingMethod();
        
        SessionManager::G()->checkCsrf();
        C::assignExceptionHandler(SessionManager::G()::ExceptionClass(), [static::class, 'OnSessionException']);
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
        $url_reg = __url('register');
        $url_login = __url('login');
        C::Show(get_defined_vars(), 'main');
    }
    public function register()
    {
        $csrf_field = SessionManager::G()->csrf_field();
        $url_register = __url('register');
        C::Show(get_defined_vars(), 'register');
    }
    public function login()
    {
        $csrf_field = SessionManager::G()->csrf_field();
        $url_login = __url('login');
        C::Show(get_defined_vars(),'login');
    }
    public function logout()
    {
        SessionManager::G()->logout();
        C::ExitRouteTo('index');
    }
    ////////////////////////////////////////////
    public function do_register()
    {
        $post = C::POST();
        try {
            $user = UserBusiness::G()->register($post);
            SessionManager::G()->setCurrentUser($user);
            C::ExitRouteTo($this->url_home);  // 我们要把这 url_home 搞成可配置化
        } catch (\Exception $ex) {
            $error = $ex->getMessage();
            $name = C::POST('name', '');
            C::Show(get_defined_vars(), 'register');
            return;
        }
        ;
    }
    public function do_login()
    {
        $post = C::POST();
        try {
            $user = UserBusiness::G()->login($post);
            SessionManager::G()->setCurrentUser($user);
            C::ExitRouteTo($this->url_home);
        } catch (\Exception $ex) {
            $error = $ex->getMessage();
            $name =  __h( C::POST('name', ''));
            C::Show(get_defined_vars(), 'login');
            return;
        }
        
    }
}