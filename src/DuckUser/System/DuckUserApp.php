<?php declare(strict_types=1);
/**
 * DuckPHP
 * From this time, you never be alone~
 */
namespace DuckUser\System;

use DuckPhp\DuckPhp;
use DuckPhp\Component\DbManager;
use DuckUser\System\ActionApi;
use DuckUser\Controller\ExceptionReporter;
use DuckPhp\Ext\InstallerTrait;

class DuckUserApp extends DuckPhp
{
    use InstallerTrait;
    
    //@override
    public $options = [
        'path' => __DIR__ . '/../',
        
        'exception_reporter' => ExceptionReporter::class,
        'class_user' => User::class,
        
        //'table_prefix' => '',   // 表前缀
        //'session_prefix' => '',  // Session 前缀
        
        'home_url' => 'Home/index',
    ];
}