<?php declare(strict_types=1);
/**
 * DuckPhp
 * From this time, you never be alone~
 */
namespace DuckAdmin\System;

use DuckPhp\DuckPhp;

class DuckAdminApp extends DuckPhp
{
    public $options = [
        'is_debug' => true,
        'path' => __DIR__.'/../../',
        'namespace' => 'DuckAdmin',
        'namespace_controller' => 'System',
        'installed' => true,
        'data_file_enable' => true,
        'duckcoverage_enable' => true,

        'app' => [
//*
            \DuckAdmin\Admin\System\AdminApp::class => [
                'controller_url_prefix' => 'admin/',
                'admin_provider_enable' => false,
            ],
//*/
//*
            \DuckAdmin\DemoUsers\System\DemoUsersApp::class => [
                'controller_url_prefix' => 'demo_users/',
                'demo_users'=>[
                    't1'=>'123456',
                    't2'=>'123456',
                ],
                'user_provider_enable' => false,
            ],
//*/
//*
            \DuckAdmin\SingleAdmin\System\SingleAdminApp::class => [
                'controller_url_prefix' => 'single_admin/',
                'single_admin_password' => '123456',
                'admin_provider_enable' => false,
            ],
//*/
//*
            \DuckAdmin\User\System\UserApp::class => [
                'controller_url_prefix' => 'user/',
                'user_provider_enable' => false,
            ],
//*/
//*
            \DuckAdmin\SimpleBlog\System\SimpleBlogApp::class => [
                'controller_url_prefix' => 'blog/',
                'admin_provider_enable' => false,
            ],
//*/
            \DuckAdmin\Admin_LaravelModel\System\LaravelModelApp::class => [
                'controller_url_prefix' => 'admin_laravel/',
                'admin_provider_enable' => false,
            ],
            \DuckAdmin\Admin_ThinkPHPModel\System\ThinkPHPModelApp::class => [
                'controller_url_prefix' => 'admin_thinkphp/',
                'admin_provider_enable' => false,
            ],
        ],

        'duckcoverage_web_base_url' => 'http://admin.duckphp-local.com/',
        'duckcoverage_test_lister'=> [\DuckAdmin\Admin\System\TestLister::class ,'GetTestList'],

        'duckadmin_admin_provider' => \DuckAdmin\SingleAdmin\System\SingleAdminApp::class,
        'duckadmin_user_provider' => \DuckAdmin\DemoUsers\System\DemoUsersApp::class,

    ];
    protected function onPrepare(): void
    {
        parent::onPrepare();
        $this->options['duckcoverage_path_src'] = realpath(__DIR__ . '/../../') . '/src/';
        
        if(class_exists(\DuckCoverage\DuckCoverage::class) && $this->_Setting('duckcoverage_enable')){
            \DuckCoverage\DuckCoverage::Prepare([]);
        }
        
    }
    protected function onInit():void
    {
        parent::onInit();

        if(!class_exists(\think\Container::class)){
            $this->options['app'][\DuckAdmin\Admin_ThinkPHPModel\System\ThinkPHPModelApp::class] = false;
        }
        if(!class_exists(\Illuminate\Database\Capsule\Manager::class)){
            $this->options['app'][\DuckAdmin\Admin_LaravelModel\System\LaravelModelApp::class] = false;
        }
        
        $this->options['app'][$this->options['duckadmin_admin_provider']]['admin_provider_enable'] = true;
        $this->options['app'][$this->options['duckadmin_user_provider']]['user_provider_enable'] = true;
    }
    protected function onInited():void
    {
        parent::onInited();
    }
}
