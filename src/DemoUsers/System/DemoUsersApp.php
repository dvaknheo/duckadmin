<?php declare(strict_types=1);
/**
 * DuckAdmin DemoUsers - 预设用户系统
 * 不使用数据库、不使用安装系统;登录限定 demo_users 数组中的用户
 */
namespace DuckAdmin\DemoUsers\System;

use DuckPhp\DuckPhp;

use DuckAdmin\DemoUsers\Controller\AppAction;

class DemoUsersApp extends DuckPhp
{
    //@override
    public $options = [
        'is_debug' =>true,
        'path' => __DIR__ . '/../',
        'namespace' => "DuckAdmin\\DemoUsers",
        'name' => 'DemoUsers',
        'installed' => true,
        'duckcoverage_test_lister' => [TestLister::class ,'GetTestOrderList'],
        'error_404' => '_sys/error_404',
        'error_500' => '_sys/error_500',
        'user_view_file_header' => '_sys/inc-head',
        'user_view_file_footer' => '_sys/inc-foot',


        'user_provider' => GlobalUserEx::class,

        'user_callback_for_local_service' => [AppAction::class, 'localService'],
        'user_callback_for_login_service' => [AppAction::class, 'localService'],
        'user_callback_for_login_session' => [AppAction::class, 'getSession'],

        'user_url_home' => 'Home/index',
        'user_url_login' => 'index',
        'user_url_logout' => 'logout',
        'user_url_regist' => 'index',

        'demo_users' => [
        ],

    ];
}
