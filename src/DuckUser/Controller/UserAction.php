<?php declare(strict_types=1);
/**
 * DuckPHP
 * From this time, you never be alone~
 */
namespace DuckUser\Controller;

use DuckPhp\Foundation\SimpleActionTrait;
use DuckPhp\Helper\ControllerHelperTrait;

use DuckUser\Business\UserBusiness;
use DuckUser\System\DuckUser;
use DuckUser\System\Session;
use DuckUser\System\ProjectException;
//use DuckUser\System\BusinessException;
//use DuckUser\System\ControllerException;

class UserAction
{
    use SimpleActionTrait;
    use ControllerHelperTrait;
    
    public function id()
    {
        $user = Session::G()->getCurrentUser();
        static::ThrowOn(!$user, '请登录');
        return $user['id'];
    }
    public function data()
    {
        return Session::G()->getCurrentUser();
    }
    public function register($post)
    {
        $user = UserBusiness::G()->register($post);
        Session::G()->setCurrentUser($user);
    }
    public function login()
    {
        $user = UserBusiness::G()->login($post);
        Session::G()->setCurrentUser($user);
    }
    public function logout()
    {
        Session::G()->unsetCurrentUser();
    }
    //////////////
    public static function GoHome()
    {
        return static::G()->_GoHome();
    }
    protected function _GoHome()
    {
        static::ExitRouteTo(DuckUser::G()->options['home_url']);
    }
    ////////////
    public static function UserId()
    {
        return static::_()->id();
    }
    public function csrfToken()
    {
        return Session::G()->csrfToken();
    }
    
    public function checkCsrf()
    {
        if( empty(static::POST()) ){ return ;}
        $referer = static::SERVER('HTTP_REFERER','');
        $domain = static::Domain(true).'/';
        $token = static::Post('_token');
        
        static::ThrowOn((substr($referer, 0, strlen($domain)) !== $domain), "CRSF", 419);
        
        $session_token =  Session::G()->getToken();
        //static::ThrowOn($token !== $session_token, "csrf_token 失败[$token !== $session_token]", 419);
    }
    public function isCsrfException($ex)
    {
        return is_a($ex, \Exception::class) && $ex->getCode=419;
    }
    public function csrfField()
    {
        return Session::G()->csrfField();
    }
    /////////////
    public function initController($class)
    {
        static::assignExceptionHandler(ProjectException::class, [ExceptionReporter::class, 'OnException']);
        //Helper::assignExceptionHandler(BusinessException::class, [ExceptionReporter::class, 'OnException']);
        //Helper::assignExceptionHandler(CotrollerException::class, [ExceptionReporter::class, 'OnException']);
        
        $this->checkCsrf();
        
        $csrf_token = $this->csrfToken();
        $csrf_field = $this->csrfField();
         
        $user = $this->data();
        $user_name = $user['username'] ?? '';
        static::setViewHeadFoot('home/inc-head','home/inc-foot');
        static::assignViewData(get_defined_vars());
    }
}