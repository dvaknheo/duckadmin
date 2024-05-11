<?php declare(strict_types=1);
/**
 * DuckPHP
 * From this time, you never be alone~
 */
namespace DuckUser\System;

use DuckPhp\DuckPhp;
use DuckUser\Controller\ExceptionReporter;

class DuckUserApp extends DuckPhp
{
    //@override
    public $options = [
        'path' => __DIR__ . '/../',
        'namespace' => 'DuckUser',
        'exception_reporter' => ExceptionReporter::class,
        'class_user' => User::class,
        
        'need_install'=>true,
        'database_driver'=>'sqlite',
        'local_database' =>true,
        'cli_command_with_fast_installer' => true,
        
        'home_url' => 'Home/index',
        
        //'table_prefix' => '',   // 表前缀
        //'session_prefix' => '',  // Session 前缀
    ];
}