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
    
    public function onSessionException($ex = null)
    {
        if(!isset($ex)){
            Helper::Show404();
            return;
        }
        $code = $ex->getCode();
        __logger()->warning(''.(get_class($ex)).'('.$ex->getCode().'): '.$ex->getMessage());
        if (Session::_()->isCsrfException($ex) && __is_debug()) {
            Helper::exit(0);
        }
        Helper::Show302('login');
    }

}