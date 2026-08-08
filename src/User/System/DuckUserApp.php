<?php declare(strict_types=1);
/**
 * DuckPHP
 * From this time, you never be alone~
 */
namespace DuckAdmin\User\System;

use DuckPhp\DuckPhp;
use DuckPhp\Ext\RouteHookWebInstaller;
use DuckPhp\GlobalUser\GlobalUser;
use DuckAdmin\User\Controller\ExceptionReporter;
use DuckAdmin\User\Controller\UserAction;

class DuckUserApp extends DuckPhp
{
    //@override
    public $options = [
        'path' => __DIR__ . '/../',
        'namespace' => "DuckAdmin\User",
        'name'  => 'DuckUser',

        'data_file_enable' => true,
        'ext' => [
            RouteHookWebInstaller::class => true,
        ],
        'web_installer_use_database' => true,
        'web_installer_database_drivers' => ['sqlite' => true],

        'exception_reporter' => ExceptionReporter::class,
        'exception_for_project'  => ProjectException::class,
        'exception_for_business'  => BusinessException::class,
        'exception_for_controller'  => ControllerException::class,
        
        //'table_prefix' => '',   // 表前缀
        'session_prefix' => 'duckuser_',  // Session 前缀
        
        /////////////////
        'class_user' => GlobalUser::class,
        'user_callback_for_id' =>       [UserAction::class,'id'],
        'user_callback_for_name' =>     [UserAction::class,'name'],
        'user_callback_for_local_service' =>  [UserAction::class,'service'],
        'user_url_home' => 'Home/index',
        'user_url_regist' => 'register',
        'user_url_login' => 'login',
        'user_url_logout' => 'logout',
        'user_view_file_header' => '_sys/inc-head',
        'user_view_file_footer' => '_sys/inc-foot',

        ///////////////////
        'home_url' => 'Home/index',
    ];
    public function _On404(): void
    {
        //var_dump(\DuckPhp\Core\Route::_()->getRouteError());
    }
}