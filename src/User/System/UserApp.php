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
            RouteHookWebInstaller::class => [
                'web_installer_use_redis' => false,
                'web_installer_use_database' => true,
                'web_installer_database_drivers' => ['sqlite' => true],
                'web_installer_view' => 'install',

            ],
            GlobalUser::class => [
                'globaluser_local_service' =>  [UserAction::class,'service'],
                'globaluser_login_service' =>  [UserAction::class,'service'],
                'globaluser_login_session' => [UserAction::class,'session'],

                'globaluser_url_home' => 'Home/index',
                'globaluser_url_register' => 'register',
                'globaluser_url_login' => 'login',
                'globaluser_url_logout' => 'logout',
                'globaluser_view_file_header' => '_sys/inc-head',
                'globaluser_view_file_footer' => '_sys/inc-foot',
            ]
        ],

        ///////
        //'table_prefix' => '',
        //'session_prefix' => 'duckuser_',
        'user_provider_enable' => true,
        'url_user_home' => 'Home/index',
        'duckcoverage_test_lister'=> [TestLister::class ,'GetTestList'],
    ];
}