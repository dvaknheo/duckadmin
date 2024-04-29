<?php declare(strict_types=1);
/**
 * 这里我们做一下
 */
namespace DuckUser\System;

use DuckPhp\Foundation\CommonCommandTrait;
use DuckPhp\Foundation\FastInstallerTrait;
use DuckPhp\Foundation\SimpleSingletonTrait;

/**
 * 入口类
 */
class Command
{
    use SimpleSingletonTrait;
    use CommonCommandTrait;
    use FastInstallerTrait;
}
