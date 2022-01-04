<?php declare(strict_types=1);
/**
 * DuckPhp
 * From this time, you never be alone~
 */

namespace DuckUser\System;

use DuckPhp\Foundation\SimpleControllerTrait;
use DuckPhp\Helper\ControllerHelperTrait;

class ProjectController
{
    use SimpleControllerTrait;
    use ControllerHelperTrait;
    
    //// 以下是专有助手方法 ////
    protected static function GoHome()
    {
        return static::G()->_GoHome();
    }
    protected function _GoHome()
    {
        static::ExitRouteTo(App::G()->options['home_url']);
    }
}
