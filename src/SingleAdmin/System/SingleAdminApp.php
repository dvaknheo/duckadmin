<?php declare(strict_types=1);
/**
 * DuckAdmin SingleAdmin - 极简后台
 * 不使用数据库、不使用安装系统;single_admin_password 作为超级管理员登录密码
 */
namespace DuckAdmin\SingleAdmin\System;

use DuckPhp\DuckPhp;
use DuckPhp\GlobalAdmin\GlobalAdmin;

use DuckAdmin\SingleAdmin\Controller\AdminAction;

class SingleAdminApp extends DuckPhp
{
    //@override
    public $options = [
        'path' => __DIR__ . '/../',
        'namespace' => "DuckAdmin\\SingleAdmin",
        'name' => 'SingleAdmin',
        'installed' =>true,

        'ext' => [
             GlobalAdmin::class =>[
                'globaladmin_local_service' => [AdminAction::class, 'localService'],
                'globaladmin_login_service' => [AdminAction::class, 'localService'],
                'globaladmin_login_session' => [AdminAction::class, 'session'],

                'globaladmin_url_home' => 'Home/index',
                'globaladmin_url_login' => 'index',
                'globaladmin_url_logout' => 'logout',

                'globaladmin_view_file_header' => '_sys/inc-head',
                'globaladmin_view_file_footer' => '_sys/inc-foot',

            ],
        ],

        'admin_provider_enable' => true,
        'duckcoverage_test_lister' => [TestLister::class ,'GetTestOrderList'],
        'single_admin_password' => '',
    ];
    //@override
    protected function onInited(): void
    {
        parent::onInited();
    }
}
