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
                    'webinstaller.h1' => 'DuckPhp Web Installer',
                    'webinstaller.install_complete' => 'Install Complete',
                    'webinstaller.congratulations' => 'Congratulations! The application has been installed successfully.',
                    'webinstaller.install_redirect' => 'Installation successful. Redirecting to the homepage in 5 seconds.',
                    'webinstaller.manual_redirect' => 'Go to homepage now',
                    'webinstaller.env_check' => 'Environment Check',
                    'webinstaller.current_controller_prefix' => 'Current controller_resource_prefix',
                    'webinstaller.item' => 'Item',
                    'webinstaller.status' => 'Status',
                    'webinstaller.redis_config' => 'Redis Config',
                    'webinstaller.follow_main_application' => 'Follow Main Application',
                    'webinstaller.no_redis_in_root' => 'Main application has no redis configured.',
                    'webinstaller.no_database_in_root' => 'Main application has no database configured.',
                    'webinstaller.host' => 'Host',
                    'webinstaller.port' => 'Port',
                    'webinstaller.auth' => 'Auth',
                    'webinstaller.select' => 'Select',
                    'webinstaller.multi_redis_hint' => 'Multiple redis configs are supported: add more entries to the config file manually after install.',
                    'webinstaller.database_config' => 'Database Config',
                    'webinstaller.driver' => 'Driver',
                    'webinstaller.file' => 'File',
                    'webinstaller.dbname' => 'Database',
                    'webinstaller.username' => 'Username',
                    'webinstaller.password' => 'Password',
                    'webinstaller.multi_db_hint' => 'Multiple database configs (master/slave) are supported: add more entries to the config file manually after install.',
                    'webinstaller.force_reinstall' => 'Force reinstall (drop existing tables)',
                    'webinstaller.customer_setting' => 'Customer Setting',
                    'webinstaller.install' => '安装',
                
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
        //'customer_duckcoverage_short' => true,
        //'with_install_coverage' => true,
    ];
    //@override
    protected function onInited(): void
    {
        parent::onInited();
        // if($this->options['short_duckcoverage']){
        //     $this->options['duckcoverage_test_lister'] =[TestLister::class, 'ShortTestList'];
        // }
    }
}

