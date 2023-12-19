<?php declare(strict_types=1);
/**
 * DuckPHP
 * From this time, you never be alone~
 */
namespace SimpleBlog\Controller;
use DuckPhp\Foundation\ExceptionReporterTrait;

class ExceptionReporter
{
    use ExceptionReporterTrait;
    
    public function onProjectException($ex = null)
    {
        //
    }
    public function onUserException($ex = null)
    {
        //
    }
}