<?php declare(strict_types=1);
/**
 * DuckPhp
 * From this time, you never be alone~
 */
namespace DuckAdmin\System;

use DuckPhp\DuckPhp;
use DuckPhp\Component\ExtOptionsLoader;

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

        // 语言：url/cookie/浏览器都没给提示时的默认值。子应用的 Lang 通过
        // lang_follow_root 跟随根应用，所以在这里设一处即可。
        // 安装页等文案来自各子应用的 config/lang-{语言}-for_webinstaller.php；
        // 想临时看英文：加 ?lang=en（或让浏览器发 Accept-Language: en）。
        'lang_default' => 'zh_CN',

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
                    't2'=>'654321',
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
        // 根应用的测试列表:它只发 #INCLUDE_CHILD,由子应用各自的 lister 提供具体步骤。
        // (不要指到某个子应用的 lister:那样 phase 不会切过去,相对 url 也就带不上子应用前缀)
        'duckcoverage_test_lister'=> [\DuckAdmin\System\TestLister::class, 'GetTestList'],

        'data' => [
            'duckadmin_admin_provider' => \DuckAdmin\SingleAdmin\System\SingleAdminApp::class,
            'duckadmin_user_provider' => \DuckAdmin\DemoUsers\System\DemoUsersApp::class,
        ],

    ];
    protected function onPrepare(): void
    {
        //@codeCoverageIgnoreStart
        parent::onPrepare();
        $this->options['duckcoverage_path_src'] = realpath(__DIR__ . '/../../') . '/src/';

        // 覆盖测试(duckcoverage --play)要请求的站点基址。
        // 同一份代码会部署到不同域名(例如 admin- / bak-admin- / bak-duckadmin-),
        // 所以允许由环境配置 config/DuckPhpSettings.config.php 里的
        // 'duckcoverage_web_base_url' 覆盖,缺省沿用本类 options 里的值。
        $this->options['duckcoverage_web_base_url'] = (string)$this->_Setting(
            'duckcoverage_web_base_url',
            $this->options['duckcoverage_web_base_url'] ?? ''
        );

        if(class_exists(\DuckCoverage\DuckCoverage::class) && $this->_Setting('duckcoverage_enable')){
            \DuckCoverage\DuckCoverage::Prepare([]);
        }
        //@codeCoverageIgnoreEnd
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
        
        $admin_provider = $this->options['data']['duckadmin_admin_provider'] ?? '';
        if (isset($this->options['app'][$admin_provider])) {
            $this->options['app'][$admin_provider]['admin_provider_enable'] = true;
        }
        $user_provider = $this->options['data']['duckadmin_user_provider'] ?? '';
        if (isset($this->options['app'][$user_provider])) {
            $this->options['app'][$user_provider]['user_provider_enable'] = true;
        }
    }
    public function saveProviders($admin_provider, $user_provider)
    {
        $data = [];
        if (isset($this->options['app'][$admin_provider])) {
            $data['duckadmin_admin_provider'] = $admin_provider;
        }
        if (isset($this->options['app'][$user_provider])) {
            $data['duckadmin_user_provider'] = $user_provider;
        }

        ExtOptionsLoader::_()->saveExtOptions($data);
    }

}
