<?php declare(strict_types=1);
/**
 * DuckPHP
 * From this time, you never be alone~
 */
namespace DuckUser\Controller;
use DuckPhp\Foundation\ExceptionReportTrait;

class ExceptionReport
{
    use ExceptionReportTrait;
    
    public static function OnException($ex)
    {
        $class = get_class($ex);
        $class = basename(str_replace("\\","/",$class));
        //这里还要有命名空间前缀的问题
        return ([static::class,$method])();
    }
    
    public static function OnProjectException($ex)
    {
        var_dump(__METHOD__);
        //$x ::getCode;
        // 然后怎么分不同的Code
        // 我们这里想把异常分来现实
        
    }
    public function onBusinessException($ex)
    {
        var_dump(__METHOD__);
        //
    }
    public static function OnControllerException($ex)
    {
        var_dump(__METHOD__);
        //
    }
    public function onSessionException($ex = null)
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