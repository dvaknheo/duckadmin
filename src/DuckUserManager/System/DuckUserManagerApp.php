<?php declare(strict_types=1);
/**
 * 这里我们做一下
 */
namespace DuckUserManager\System;

use DuckPhp\DuckPhp;
use DuckPhp\FastInstaller\FastInstaller;

/**
 * 入口类
 */
class DuckUserManagerApp extends DuckPhp
{
    //@override
    public $options = [
        'path' => __DIR__ . '/../',
        'database_driver'=>'sqlite',
        'cli_command_classes' => [FastInstaller::class],
    ];
}