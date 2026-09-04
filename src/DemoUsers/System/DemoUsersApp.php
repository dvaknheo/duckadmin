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
        'duckcoverage_callback' => [self::class ,'getTestOrderList'],

        'user_provider' => GlobalUser::class,

        'user_callback_for_id' =>      [AppAction::class, 'id'],
        'user_callback_for_name' =>    [AppAction::class, 'name'],
        'user_callback_for_data' =>    [AppAction::class, 'data'],
        'user_callback_for_local_service' => [AppAction::class, 'localService'],

        'user_url_home' => 'Home/index',
        'user_url_login' => '',       // 默认 index(MainController)
        'user_url_logout' => 'logout',
        'user_url_regist' => '',

        'user_view_file_header' => '_sys/inc-head',
        'user_view_file_footer' => '_sys/inc-foot',

        // 错误页面
        'error_404' => '_sys/error_404',
        'error_500' => '_sys/error_500',

        'demo_usersx' => [
            't1'=>'12456',
            't2'=>'',
        ],
    ];
    public function __construct()
    {
        parent::__construct();
        $this->options['duckcoverage_callback'] = [static::class ,'getTestOrderList'];
    }
    public static function getTestOrderList(): string
    {
        //
        return '';
    }
}
