<?php declare(strict_types=1);
/**
 * DuckPhp
 * From this time, you never be alone~
 */

namespace DuckUser\Controller;

use DuckPhp\Foundation\SimpleSingletonTrait;
use DuckPhp\Helper\ControllerHelperTrait;
use DuckPhp\Core\App;
class Helper
{
    use SimpleSingletonTrait;
    use ControllerHelperTrait;
    
    public function goHome()
    {
        static::ExitRouteTo(App::Current()->options['home_url']);
    }
    public function csrfToken()
    {
        return Session::_()->csrfToken();
    }
    
    public function checkCsrf()
    {
        if ( empty(static::POST()) ) { return ;}
        $referer = static::SERVER('HTTP_REFERER','');
        $domain = static::Domain(true).'/';
        $token = static::Post('_token');
        
        static::ThrowOn((substr($referer, 0, strlen($domain)) !== $domain), "CRSF", 419);
        
        $session_token =  Session::_()->getToken();
        //static::ThrowOn($token !== $session_token, "csrf_token 失败[$token !== $session_token]", 419);
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