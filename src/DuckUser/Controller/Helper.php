<?php declare(strict_types=1);
/**
 * DuckPhp
 * From this time, you never be alone~
 */

namespace DuckUser\Controller;

use DuckPhp\Helper\ControllerHelperTrait;
use DuckPhp\Core\ExitException;
use DuckPhp\Core\CoreHelper;
class Helper
{
    use ControllerHelperTrait;
    public static function isExitException($ex)
    {
        return \is_a($ex,ExitException::class);
    }
    
    public function goHome()
    {
        try{
            Helper::Show302(UserAction::_()->urlForHome());
        }catch(\Exception $ex){
            if($this->isExitException($ex)){
                throw $ex;
            }
        }
        return;
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
        
        ControllerException::ThrowOn((substr($referer, 0, strlen($domain)) !== $domain), "CRSF", 419);
        
        $session_token =  Session::_()->getToken();
        //ControllerException::ThrowOn($token !== $session_token, "csrf_token 失败[$token !== $session_token]", 419);
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