<?php declare(strict_types=1);
/**
 * DuckPHP
 * From this time, you never be alone~
 */
namespace DuckUser\System;

use DuckPhp\DuckPhp;
use DuckPhp\GlobalUser\GlobalUser;
use DuckUser\Controller\ExceptionReporter;
use DuckUser\Controller\UserAction;

class DuckUserApp extends DuckPhp
{
    //@override
    public $options = [
        'path' => __DIR__ . '/../',
        'namespace' => 'DuckUser',
        'class_user' => GlobalUser::class,
        
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
        'user_callback_for_id' =>       [UserAction::class,'id'],
        'user_callback_for_name' =>     [UserAction::class,'name'],
        'user_callback_for_data' =>     [UserAction::class,'data'],
        'user_callback_for_local_service' =>  [UserAction::class,'service'],
        'user_url_home' => 'Home/index',
        'user_url_regist' => 'register',
        'user_url_login' => 'login',
        'user_url_logout' => 'logout',
        
        ///////////////////
        'home_url' => 'Home/index',
        
    ];
}