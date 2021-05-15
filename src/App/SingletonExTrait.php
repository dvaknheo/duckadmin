<?php declare(strict_types=1);
/**
 * DuckPhp
 * From this time, you never be alone~
 */

namespace DuckAdmin\App;
trait SingletonExTrait
{
// 用于给 Base 隔离 DuckPhp 命名空间
    use \DuckPhp\SingletonEx\SingletonExTrait;
}
