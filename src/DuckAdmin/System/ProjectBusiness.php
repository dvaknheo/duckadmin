<?php declare(strict_types=1);
/**
 * DuckPhp
 * From this time, you never be alone~
 */

namespace DuckAdmin\System;

use DuckPhp\Foundation\ThrowOnableTrait;
use DuckPhp\Helper\BusinessHelperTrait;
use DuckPhp\SingletonEx\SingletonExTrait;
/**
 * 工程动作基类，绑定了助手，和 ThrowOn()
 */
class ProjectBusiness
{
    use SingletonExTrait;
    use BusinessHelperTrait;
    use ThrowOnableTrait;
}
