<?php declare(strict_types=1);
/**
 * DuckPHP
 * From this time, you never be alone~
 */
namespace DuckUser\System;

use DuckPhp\DuckPhp;
use DuckPhp\Foundation\CommonCommandTrait;
use DuckPhp\Foundation\FastInstallerTrait;

use DuckUser\System\ActionApi;
use DuckUser\Controller\ExceptionReporter;

class DuckUserApp extends DuckPhp
{
    use CommonCommandTrait;
    use FastInstallerTrait;
    
    //@override
    public $options = [
        'path' => __DIR__ . '/../',
        'cli_command_class' => null,
        
        'exception_reporter' => ExceptionReporter::class,
        'class_user' => User::class,
        
        //'table_prefix' => '',   // 表前缀
        //'session_prefix' => '',  // Session 前缀
        
        'home_url' => 'Home/index',
    ];
}