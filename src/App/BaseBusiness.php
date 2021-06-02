<?php declare(strict_types=1);
/**
 * DuckPhp
 * From this time, you never be alone~
 */

namespace DuckAdmin\App;

use DuckPhp\SingletonEx\SingletonExTrait;
use DuckPhp\Helper\BusinessHelperTrait;

class BaseBusiness
{
    use SingletonExTrait;
    use BusinessHelperTrait;
}
