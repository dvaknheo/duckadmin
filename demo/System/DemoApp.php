<?php declare(strict_types=1);
/**
 * DuckPhp
 * From this time, you never be alone~
 */
namespace DuckAdminDemo\System;

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

        'app' => [
//*
            \DuckAdmin\Admin\System\AdminApp::class => [
                'controller_url_prefix' => 'admin/',

                //'customer_duckcoverage_short' =>  false,
                //'customer_duckcoverage_clean' =>  false,
            ],
//*/
//*
            \DuckAdmin\DemoUsers\System\DemoUsersApp::class => [
                'controller_url_prefix' => 'users/',

                'demo_users'=>[
                    't1'=>'123456',
                    't2'=>'123456',
                ],
            ],
//*/
//*

            \DuckAdmin\SingleAdmin\System\SingleAdminApp::class => [
                'controller_url_prefix' => 'single/',

                'single_admin_password' => '123456',
            ],
//*/
//*
            \DuckAdmin\User\System\UserApp::class => [
                'controller_url_prefix' => 'fulluser/',             // 访问路径
            ],
//*/
//*
            \DuckAdmin\SimpleBlog\System\SimpleBlogApp::class => [
                'controller_url_prefix' => 'blog/',
            ],
//*/

        ],

        'duckcoverage_test_lister'=> [TestLister::class ,'GetTestList'],
        'duckcoverage_web_base_url' => 'http://admin.duckphp-local.com/',
        'duckcoverage_report_direct ' => false,

        'demoapp_admin_provider' => \DuckAdmin\Admin\System\AdminApp::class,
        'demoapp_user_provider' => \DuckAdmin\DemoUsers\System\DemoUsersApp::class,

        //'demoapp_cover_doing' => \DuckAdmin\Admin\System\AdminApp::class,
        //'demoapp_cover_subonly' => \DuckAdmin\Admin\System\AdminApp::class,

    ];
    protected function onPrepare(): void
    {
        parent::onPrepare();

        foreach($this->options['app'] as &$app){
            $app['admin_provider_enable'] = false;
            $app['user_provider_enable'] = false;
        }
        unset($app);
        $this->options['app'][$this->options['demoapp_admin_provider']]['admin_provider_enable'] = true;
        $this->options['app'][$this->options['demoapp_user_provider']]['user_provider_enable'] = true;
        //



        if(class_exists(\DuckCoverage\DuckCoverage::class)){
            $this->options['duckcoverage_path_src'] = realpath(__DIR__ . '/../../') . '/src/';
            \DuckCoverage\DuckCoverage::Prepare([]);
        }
    }
    protected function onInited():void
    {
        parent::onInited();
    }
}
