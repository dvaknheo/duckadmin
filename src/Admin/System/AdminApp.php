<?php declare(strict_types=1);
/**
 * DuckPhp Admin System
 * From this time, you never be alone~
 */
namespace DuckAdmin\Admin\System;

use DuckPhp\DuckPhp;
use DuckPhp\Ext\RouteHookWebInstaller;
use DuckPhp\GlobalAdmin\GlobalAdmin;

use DuckAdmin\Admin\Controller\AdminAction;

class AdminApp extends DuckPhp
{
    //@override
    public $options = [
        'path' => __DIR__ . '/../',
        'namespace' => "DuckAdmin\\Admin",
        'name' => 'DuckAdmin',
        
        'data_file_enable' => true,
        'ext' => [
            RouteHookWebInstaller::class => true,
        ],
        'web_installer_use_redis' => false,
        'web_installer_use_database' => true,
        'web_installer_database_drivers' => ['sqlite' => true],
        // 后面还要加自定义 view 和 callback

        // 错误页面
        'error_404' => '_sys/error_404',
        'error_500' => '_sys/error_500',

        // 异常处理
        'exception_for_project'  => ProjectException::class,
        'exception_for_business'  => BusinessException::class,
        'exception_for_controller'  => ControllerException::class,
        'exception_reporter' => ExceptionReporter::class,
        
        // 控制器类名调整：自动首字母大写
        'controller_class_adjust' => 'uc_class',

        'class_admin' => GlobalAdmin::class,

        'admin_url_home' => 'Dashboar/index',
        'admin_url_login' => 'Login/login',
        'admin_url_logout' => 'Login/logout',

        'admin_view_file_header' => 'admin/header',
        'admin_view_file_footer' => 'admin/footer',
        'admin_callback_for_id' =>      [AdminAction::class,'id'],
        'admin_callback_for_name' =>    [AdminAction::class,'name'],
        'admin_callback_for_data' =>    [AdminAction::class,'data'],
        'admin_callback_for_local_service' => [AdminAction::class,'localService'],
        'admin_callback_for_add_ext_view_data' => [AdminAction::class,'addExtViewData'],

    ];
    //@override
    protected function onInited(): void
    {
        parent::onInited();
    }
}
