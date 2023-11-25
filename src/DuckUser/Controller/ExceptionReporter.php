<?php declare(strict_types=1);
/**
 * DuckPHP
 * From this time, you never be alone~
 */
namespace DuckUser\Controller;
use DuckPhp\Foundation\ExceptionReporterTrait;

class ExceptionReporter
{
    use ExceptionReporterTrait;
    
    public function tonProjectException($ex)
    {
        var_dump(__METHOD__);
        //$x ::getCode;
        // 然后怎么分不同的Code
        // 我们这里想把异常分来现实
        
    }
    public function tonBusinessException($ex)
    {
        var_dump(__METHOD__);
        //
    }
    public function tonControllerException($ex)
    {
        var_dump($ex);
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