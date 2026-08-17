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

        // 超级管理员登录密码(无数据库)
        'single_admin_password' => '123456',

        // 保留 duckphp 的 GlobalAdmin,由 AdminAction 提供 admin_callback_* 实现
        'admin_provider' => GlobalAdmin::class,

        'admin_callback_for_id' =>      [AdminAction::class, 'id'],
        'admin_callback_for_name' =>    [AdminAction::class, 'name'],
        'admin_callback_for_data' =>    [AdminAction::class, 'data'],
        'admin_callback_for_local_service' => [AdminAction::class, 'localService'],
        'admin_callback_for_add_ext_view_data' => [AdminAction::class, 'addExtViewData'],

        'admin_url_home' => 'Home/index',
        'admin_url_login' => '',
        'admin_url_logout' => 'logout',

        'admin_view_file_header' => '_sys/inc-head',
        'admin_view_file_footer' => '_sys/inc-foot',

        // 错误页面
        'error_404' => '_sys/error_404',
        'error_500' => '_sys/error_500',
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
