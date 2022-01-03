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
    
    protected static function GoHome()
    {
    }
    protected function _GoHome()
    {
        static::RouteTo(App::G()->options['home_url']);
    }
}
