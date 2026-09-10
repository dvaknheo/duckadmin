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


        'admin_provider' => GlobalAdmin::class,

        'admin_callback_for_local_service' => [AdminAction::class, 'localService'],
        'admin_callback_for_login_service' => [AdminAction::class, 'localService'],
        'admin_callback_for_session' => [AdminAction::class, 'session'],

        'admin_url_home' => 'Home/index',
        'admin_url_login' => 'index',
        'admin_url_logout' => 'logout',

        'admin_view_file_header' => '_sys/inc-head',
        'admin_view_file_footer' => '_sys/inc-foot',

        'duckcoverage_test_lister' => [TestLister::class ,'GetTestOrderList'],

        'single_admin_password' => '',
    ];
    //@override
    protected function onInited(): void
    {
        parent::onInited();
    }
    public function _OnDefaultException($ex): void  //@codeCoverageIgnore
    {
        var_dump($ex);exit; //@codeCoverageIgnore
    } //@codeCoverageIgnore
}
