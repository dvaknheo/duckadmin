<?php declare(strict_types=1);
/**
 * DuckPHP
 * From this time, you never be alone~
 */
namespace DuckUser\System;

use DuckPhp\DuckPhp;
use DuckUser\Controller\ExceptionReporter;
use DuckUser\Controller\GlobalUserAction;

class DuckUserApp extends DuckPhp
{
    //@override
    public $options = [
        'path' => __DIR__ . '/../',
        'namespace' => 'DuckUser',
        'class_user' => GlobalUserAction::class,
        
        'exception_reporter' => ExceptionReporter::class,
        'exception_for_project'  => ProjectException::class,
        'exception_for_business'  => BusinessException::class,
        'exception_for_controller'  => ControllerException::class,
        'exception_reporter' =>  ExceptionReporter::class,
        
        'controller_method_prefix' => 'action_',                    // method prefix for controllers

        //'table_prefix' => '',   // 表前缀
        'session_prefix' => 'duckuser_',  // Session 前缀
        
        //'need_install'=>true,
        /////////////////
        'home_url' => 'Home/index',
        
    ];
}