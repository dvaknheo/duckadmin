<?php declare(strict_types=1);
/**
 * DuckPhp Admin System
 * From this time, you never be alone~
 */
namespace DuckAdmin\Admin\System;

use DuckPhp\DuckPhp;
use DuckPhp\Ext\RouteHookWebInstaller;
use DuckPhp\GlobalAdmin\GlobalAdmin;

use DuckAdmin\Admin\Controller\AppAction;
use DuckAdmin\Admin\Controller\TestCommandAction;

class AdminApp extends DuckPhp
{
    //@override
    public $options = [
        'path' => __DIR__ . '/../',
        'namespace' => "DuckAdmin\\Admin",
        'name' => 'DuckAdmin',
        
        'data_file_enable' => true,
        'installed' => false,
        'error_404' => '_sys/error_404',
        'error_500' => '_sys/error_500',

        'cmd' => [
            TestCommandAction::class => true,
        ],
        'ext' => [
            RouteHookWebInstaller::class => [
                'web_installer_use_database' => true,
                'web_installer_use_redis' => false,
                'web_installer_database_drivers' => ['sqlite' => true],
                'web_installer_view' => 'install',
                'web_installer_view_block_custom' => 'install_custom',

                'web_installer_check_custom_callback' => [AppAction::class, 'Callback_CheckInstall'],
                'web_installer_do_custom_callback' => [AppAction::class, 'Callback_DoInstall'],

                'web_installer_default_sentences' => [
                    'Admin Name' => '管理员账号',
                    'Admin Password' => '管理员密码',
                    'Admin Password Confirm' => '确认密码',
                ],
            ],
            GlobalAdmin::class => [
                'globaladmin_url_home' => 'Home/index',
                'globaladmin_url_login' => 'login',
                'globaladmin_url_logout' => 'logout',
                'globaladmin_view_file_header' => '_sys/header',
                'globaladmin_view_file_footer' => '_sys/footer',
                'globaladmin_local_service' => [AppAction::class,'localService'],
                'globaladmin_login_service' => [AppAction::class,'localService'],
                'globaladmin_login_session' => [AppAction::class,'session'],
                'globaladmin_ext_view_data_callback' => [AppAction::class,'addExtViewData'],
            ],
        ],
        'admin_provider_enable' => true,

        'permission_menu_tree_for_admin' => 'AdminMenu.json',

        // duckcoverage
        'duckcoverage_test_lister' => [TestLister::class, 'GetTestList'],
        'short_duckcoverage' => true,
        'with_install_coverage' => true,
    ];
    //@override
    protected function onInited(): void
    {
        parent::onInited();
        if($this->options['short_duckcoverage']){
            $this->options['duckcoverage_test_lister'] =[TestLister::class, 'ShortTestList'];
        }
    }
}

