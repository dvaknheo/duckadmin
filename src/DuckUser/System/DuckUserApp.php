<?php declare(strict_types=1);
/**
 * DuckPHP
 * From this time, you never be alone~
 */
namespace DuckUser\System;

use DuckPhp\DuckPhp;
use DuckPhp\FastInstaller\FastInstaller;
use DuckUser\Controller\ExceptionReporter;

class DuckUserApp extends DuckPhp
{
    //@override
    public $options = [
        'path' => __DIR__ . '/../',

        'exception_reporter' => ExceptionReporter::class,
        'class_user' => User::class,
        
        'cli_command_classes' => [FastInstaller::class],
        'database_driver'=>'sqlite',
        'local_database' =>true,
        //'table_prefix' => '',   // 表前缀
        //'session_prefix' => '',  // Session 前缀
        
        'home_url' => 'Home/index',
    ];
}