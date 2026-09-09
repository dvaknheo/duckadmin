<?php declare(strict_types=1);
/**
 * DuckPhp
 * From this time, you never be alone~
 */
namespace DuckAdminDemo\System;

use DuckPhp\Core\App;
use DuckPhp\DuckPhp;
use DuckAdminDemo\System\TestLister;

class DemoApp extends DuckPhp
{
    public $options = [
        'is_debug' => true,
        'path' => __DIR__.'/../',
        'namespace' => 'DuckAdminDemo',
        'installed' => true,
        'data_file_enable' => true,

        'duckcoverage_test_lister'=> [TestLister::class ,'GetTestList'],
        'duckcoverage_web_base_url' => 'http://admin.duckphp-local.com/',
        'duckcoverage_report_direct ' => false,
        'app' => [
//*
            \DuckAdmin\Admin\System\AdminApp::class => [
                'controller_url_prefix' => 'admin/',
                'controller_resource_prefix' => 'res/',
                'admin_provider' => null,   // 关闭:admin_provider 各 admin 系统不能同时使用
                //'duckcoverage_test_lister' => null, //[TestLister::class ,'GetTestList'],
            ],
//*/
//*
            \DuckAdmin\DemoUsers\System\DemoUsersApp::class => [
                'controller_url_prefix' => 'users/',
                'is_debug'=>true,
                //'duckcoverage_test_lister' => null,
                //'user_provider' => null,
                'demo_users'=>[
                    't1'=>'123456',
                    't2'=>'123456',
                ],
            ],
//*/
/*
            \DuckAdmin\System\DuckAdminApp => [

            ],
//*/
/*
            \DuckAdmin\SimpleBlog\System\SimpleBlogApp::class => [
                'controller_url_prefix' => 'blog/',
            ],
//*/
//*

            \DuckAdmin\SingleAdmin\System\SingleAdminApp::class => [
                'controller_url_prefix' => 'single/',
                'single_admin_password' => '123456',

                //'duckcoverage_test_lister' => null,
                //'admin_provider' => null,   // 关闭:admin_provider 各 admin 系统不能同时使用
            ],
//*/
//*
            \DuckAdmin\User\System\UserApp::class => [
                'controller_url_prefix' => 'fulluser/',             // 访问路径
                //'duckcoverage_test_lister' => null,
                'is_debug'=>true,
                'controller_resource_prefix' => 'res/',    // 资源文件前缀
                'user_provider' => null,   // 关闭:user_provider 各用户系统不能同时使用
            ],
//*/
        ],
    ];
    public function __construct()
    {
        parent::__construct();
    }
    protected function onPrepare(): void
    {
        parent::onPrepare();
        if(class_exists(\DuckCoverage\DuckCoverage::class)){
            $this->options['duckcoverage_path_src'] = realpath(__DIR__ . '/../../') . '/src/User/';
            \DuckCoverage\DuckCoverage::Prepare([]);
        }
    }
    protected function onInited():void
    {

    }
}
