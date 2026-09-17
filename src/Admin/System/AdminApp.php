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
        // 错误页面
        'error_404' => '_sys/error_404',
        'error_500' => '_sys/error_500',

        'cmd' => [
            TestCommandAction::class => true,
        ],
        'ext' => [
            RouteHookWebInstaller::class => true,
        ],

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

        'use_admin_view' => true,
        'my_admin_menu_config_file' => 'AdminMenu.json',
        // 异常处理
        'exception_for_project'  => ProjectException::class,
        'exception_for_business'  => BusinessException::class,
        'exception_for_controller'  => ControllerException::class,
        'exception_reporter' => ExceptionReporter::class,
        

        'admin_provider' => GlobalAdmin::class,

        'admin_url_home' => 'Home/index',
        'admin_url_login' => 'login',
        'admin_url_logout' => 'logout',
        'admin_view_file_header' => '_sys/header',
        'admin_view_file_footer' => '_sys/footer',
        'admin_callback_for_local_service' => [AppAction::class,'localService'],
        'admin_callback_for_login_service' => [AppAction::class,'localService'],
        'admin_callback_for_session' => [AppAction::class,'session'],

        'admin_callback_for_add_ext_view_data' => [AppAction::class,'addExtViewData'],
    ];
    public function __construct()
    {
        parent::__construct();
    }
    //@override
    protected function onInited(): void
    {
        parent::onInited();
    }
}
