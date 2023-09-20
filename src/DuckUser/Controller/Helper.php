<?php declare(strict_types=1);
/**
 * DuckPHP
 * From this time, you never be alone~
 */
namespace DuckUser\Controller;
use DuckPhp\Helper\ControllerHelperTrait;

class DefaultAction
{
    use ControllerHelperTrait;
    
    public static function GoHome()
    {
        return static::G()->_GoHome();
    }
    protected function _GoHome()
    {
        static::ExitRouteTo(App::G()->options['home_url']);
    }
}