<?php declare(strict_types=1);
/**
 * DuckAdmin SingleAdmin - 极简后台
 * 不使用数据库、不使用安装系统;single_admin_password 作为超级管理员登录密码
 */
namespace DuckAdmin\SingleAdmin\System;

use DuckPhp\DuckPhp;

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

        'admin_provider' => AdminAction::class,

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
