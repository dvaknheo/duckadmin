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

class UserApp extends DuckPhp
{
    //@override
    public $options = [
        'path' => __DIR__ . '/../',
        'namespace' => "DuckAdmin\\User",
        'name'  => 'DuckUser',

        'data_file_enable' => true,
        'ext' => [
            RouteHookWebInstaller::class => true,
        ],
        'web_installer_use_redis' => false,
        'web_installer_use_database' => true,
        'web_installer_database_drivers' => ['sqlite' => true],
        'web_installer_view' => 'install',

        //'table_prefix' => '',   // 表前缀
        'session_prefix' => 'duckuser_',  // Session 前缀
        
        /////////////////
        'user_provider' => GlobalUser::class,
        'user_callback_for_local_service' =>  [UserAction::class,'service'],
        'user_callback_for_session' => [UserAction::class,'session'],

        'user_url_home' => 'Home/index',
        'user_url_register' => 'register',
        'user_url_login' => 'login',
        'user_url_logout' => 'logout',
        'user_view_file_header' => '_sys/inc-head',
        'user_view_file_footer' => '_sys/inc-foot',
        ///////////////////

        'duckcoverage_test_lister'=> [TestLister::class ,'GetTestList'],
    ];
}