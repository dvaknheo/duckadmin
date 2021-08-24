<?php declare(strict_types=1);
/**
 * DuckPhp
 * From this time, you never be alone~
 */

namespace DuckAdmin\System;

use DuckPhp\Foundation\ThrowOnableTrait;
use DuckPhp\Helper\ControllerHelperTrait;
use DuckPhp\SingletonEx\SingletonExTrait;

class ProjectAction
{
    use SingletonExTrait;
    use ControllerHelperTrait;
    use ThrowOnableTrait;
}
