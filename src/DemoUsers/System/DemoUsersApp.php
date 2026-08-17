<?php declare(strict_types=1);
/**
 * DuckAdmin DemoUsers - 预设用户系统
 * 不使用数据库、不使用安装系统;登录限定 demo_users 数组中的用户
 */
namespace DuckAdmin\DemoUsers\System;

use DuckPhp\DuckPhp;

use DuckAdmin\DemoUsers\Controller\UserAction;

class DemoUsersApp extends DuckPhp
{
    //@override
    public $options = [
        'path' => __DIR__ . '/../',
        'namespace' => "DuckAdmin\\DemoUsers",
        'name' => 'DemoUsers',

        // 预设用户数组:下标 0 空占位,用户 id = 数组下标(禁止 id=0)
        'demo_users' => [
            [],
            ['username' => 'admin', 'password' => '123456', 'name' => '管理员'],
            ['username' => 'user', 'password' => '123456', 'name' => '演示用户'],
        ],

        'class_user' => UserAction::class,
        'user_callback_for_id' =>       [UserAction::class, 'id'],
        'user_callback_for_name' =>     [UserAction::class, 'name'],
        'user_callback_for_local_service' =>  [UserAction::class, 'service'],

        'user_url_home' => 'Home/index',
        'user_url_login' => '',       // 默认 index(MainController)
        'user_url_logout' => 'logout',
        'user_url_regist' => '',

        'user_view_file_header' => '_sys/inc-head',
        'user_view_file_footer' => '_sys/inc-foot',

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
