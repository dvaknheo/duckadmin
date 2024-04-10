<?php declare(strict_types=1);
/**
 * 这里我们做一下
 */
namespace DuckUserManager\System;

use DuckPhp\DuckPhp;
use DuckPhp\Foundation\CommonCommandTrait;
use DuckPhp\Foundation\FastInstallerTrait;

/**
 * 入口类
 */
class DuckUserManagerApp extends DuckPhp
{
    use CommonCommandTrait;
    use FastInstallerTrait;
    
    //@override
    public $options = [
        'path' => __DIR__ . '/../',
        'cli_command_class' => null,
    ];
}