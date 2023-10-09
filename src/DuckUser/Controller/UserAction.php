<?php declare(strict_types=1);
/**
 * DuckPHP
 * From this time, you never be alone~
 */
namespace DuckUser\Controller;

use DuckPhp\Foundation\SimpleActionTrait;
use DuckPhp\Helper\ControllerHelper as Helper;

use DuckUser\Business\UserBusiness;
use DuckUser\System\DuckUser;
use DuckUser\System\Session;
use DuckUser\System\ProjectException;

class UserAction
{
    use SimpleActionTrait;
    
    public function id()
    {
        $user = Session::_()->getCurrentUser();
        Helper::ThrowOn(!$user, '请登录');
        return $user['id'];
    }
    public function data()
    {
        return Session::_()->getCurrentUser();
    }
    public function register($post)
    {
        $user = UserBusiness::G()->register($post);
        Session::_()->setCurrentUser($user);
    }
    public function login()
    {
        $user = UserBusiness::G()->login($post);
        Session::_()->setCurrentUser($user);
    }
    public function logout()
    {
        Session::_()->unsetCurrentUser();
    }
    //////////////
    public static function GoHome()
    {
        return Helper::G()->_GoHome();
    }
    protected function _GoHome()
    {
        Helper::ExitRouteTo(DuckUser::G()->options['home_url']);
    }
    ////////////
    public function csrfToken()
    {
        return Session::_()->csrfToken();
    }
    
    public function checkCsrf()
    {
        if( empty(Helper::POST()) ){ return ;}
        $referer = Helper::SERVER('HTTP_REFERER','');
        $domain = Helper::Domain(true).'/';
        $token = Helper::Post('_token');
        
        Helper::ThrowOn((substr($referer, 0, strlen($domain)) !== $domain), "CRSF", 419);
        
        $session_token =  Session::_()->getToken();
        //Helper::ThrowOn($token !== $session_token, "csrf_token 失败[$token !== $session_token]", 419);
    }
    public function isCsrfException($ex)
    {
        return is_a($ex, \Exception::class) && $ex->getCode=419;
    }
    public function csrfField()
    {
        return Session::_()->csrfField();
    }
    /////////////
    public function initController($class)
    {
        Helper::assignExceptionHandler(ProjectException::class, [ExceptionReporter::class, 'OnException']);
        
        $this->checkCsrf();
        
        $csrf_token = $this->csrfToken();
        $csrf_field = $this->csrfField();
         
        $user = $this->data();
        $user_name = $user['username'] ?? '';
        Helper::setViewHeadFoot('home/inc-head','home/inc-foot');
        Helper::assignViewData(get_defined_vars());
    }
}