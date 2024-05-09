<?php declare(strict_types=1);
/**
 * 这里我们做一下
 */
namespace DuckUserManager\System;

use DuckPhp\DuckPhp;

/**
 * 入口类
 */
class DuckUserManagerApp extends DuckPhp
{
    //@override
    public $options = [
        'path' => __DIR__ . '/../',
        
        //'database_driver'=>'sqlite',
        //'need_install'=>true,
        'cli_command_with_fast_installer' => true,
    ];
}