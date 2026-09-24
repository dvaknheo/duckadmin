<?php declare(strict_types=1);
/**
 * DuckAdmin DemoUsers - 预设用户系统
 * 不使用数据库、不使用安装系统;登录限定 demo_users 数组中的用户
 */
namespace DuckAdmin\DemoUsers\System;

use DuckPhp\DuckPhp;
use DuckPhp\GlobalUser\GlobalUser;

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
        'ext' =>[
            GlobalUser::class => [
                'globaluser_local_service' => [AppAction::class, 'service'],
                'globaluser_login_service' => [AppAction::class, 'service'],
                'globaluser_login_session' => [AppAction::class, 'session'],

                'globaluser_view_file_header' => '_sys/inc-head',
                'globaluser_view_file_footer' => '_sys/inc-foot',

                'globaluser_url_register' => 'index',
                'globaluser_url_login' => 'index',
                'globaluser_url_logout' => 'logout',
                'globaluser_url_home' => 'Home/index',
            ],
        ],
        'user_provider_enable' => true,
        'demo_users' => [
        ],

        'duckcoverage_test_lister' => [TestLister::class ,'GetTestOrderList'],
    ];
}
