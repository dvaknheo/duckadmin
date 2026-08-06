<?php declare(strict_types=1);
/**
 * DuckPhp Admin System
 * From this time, you never be alone~
 */
namespace DuckAdmin\Admin\System;

use DuckPhp\DuckPhp;
use DuckPhp\GlobalAdmin\GlobalAdmin;

use DuckAdmin\Admin\Controller\AdminAction;

class App extends DuckPhp
{
    //@override
    public $options = [
        'is_debug' =>true,
        'path' => __DIR__ . '/../',
        'namespace' => "DuckAdmin\\Admin",
        'name' => 'DuckAdmin',
        
        'ext' => [
            \DuckPhp\Component\ExtOptionsLoader::class => true,
        ],
        
        // 错误页面
        'error_404' => '_sys/error_404',
        'error_500' => '_sys/error_500',
        'skip_404' => false,

        // 异常处理
        'exception_for_project'  => ProjectException::class,
        'exception_for_business'  => BusinessException::class,
        'exception_for_controller'  => ControllerException::class,
        'exception_reporter' => ExceptionReporter::class,
        
        // 数据库配置 — 使用 SQLite
        'database_list' => [
            [
                'dsn' => 'sqlite:' . __DIR__ . '/../../database/admin.db',
                'username' => '',
                'password' => '',
                'driver_options' => [],
            ],
        ],
        
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
    
    public function _On404(): void
    {
        //echo \DuckPhp\Core\Route::_()->getRouteError();
        parent::_On404();
    }
}
