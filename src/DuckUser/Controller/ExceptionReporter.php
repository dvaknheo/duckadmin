<?php declare(strict_types=1);
/**
 * DuckPHP
 * From this time, you never be alone~
 */
namespace DuckUser\Controller;

class ExceptionReport
{
    public static function OnException($ex)
    {
        $class = get_class($ex);
        $class = basename(str_replace("\\","/",$class));
        return ([static::class,$method])();
    }
    
    public static function OnProjectException($ex)
    {
        var_dump(__METHOD__);
        //$x ::getCode;
        // 然后怎么分不同的Code
        // 我们这里想把异常分来现实
        
    }
    public static function OnBusinessException($ex)
    {
        var_dump(__METHOD__);
        //
    }
    public static function OnControllerException($ex)
    {
        var_dump(__METHOD__);
        //
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

}