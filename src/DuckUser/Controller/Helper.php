<?php declare(strict_types=1);
/**
 * DuckPhp
 * From this time, you never be alone~
 */

namespace DuckUser\Controller;

use DuckPhp\Helper\ControllerHelperTrait;

class Helper
{
    use ControllerHelperTrait;
    
    public function goHome()
    {
        Helper::ExitRouteTo(App::Current()->options['home_url']);
    }
    public function csrfToken()
    {
        return Session::_()->csrfToken();
    }
    
    public function checkCsrf()
    {
        if ( empty(Helper::POST()) ) { return ;}
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
}