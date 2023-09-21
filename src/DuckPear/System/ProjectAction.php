<?php declare(strict_types=1);
/**
 * DuckPhp
 * From this time, you never be alone~
 */

namespace DuckAdmin\System;

use DuckPhp\Foundation\ThrowOnableTrait;
use DuckPhp\Helper\ControllerHelperTrait;
use DuckPhp\SingletonEx\SingletonExTrait;
/**
 * 工程动作基类，绑定了控制器助手，和 ThrowOn()
 */
class ProjectAction
{
    use SingletonExTrait;
    use ControllerHelperTrait;
    use ThrowOnableTrait;
}
