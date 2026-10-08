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
        'error_500' => '_sys/error_500.php',

        'installed' => true,
        'data_file_enable' => true,

        'lang_default' => 'en-US',

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
        'duckcoverage_test_lister'=> [\DuckAdmin\System\TestLister::class, 'GetTestList'],

        'data' => [
            'duckadmin_admin_provider' => \DuckAdmin\SingleAdmin\System\SingleAdminApp::class,
            'duckadmin_user_provider' => \DuckAdmin\DemoUsers\System\DemoUsersApp::class,
        ],
    ];

    protected function onPrepare(): void
    {
        //@codeCoverageIgnoreStart
        if (function_exists('opcache_invalidate')) {
            $setting_file = $this->getProjectPath() . $this->options['setting_file'];
            @opcache_invalidate($setting_file, true);
        }
        parent::onPrepare();
        $this->options['duckcoverage_path_src'] = realpath(__DIR__ . '/../../') . '/src/';
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
        
        $this->loadProviders();
        // 覆盖测试时允许用采集标记再覆盖一次（逻辑在 CoverageGroup，与采集工具放在一起）
        \DuckAdmin\System\CoverageGroup::applyProviderFlag();
        
        $this->checkEnabled();
    }

    protected function checkEnabled(): void
    {
        if ($this->_Setting('duckadmin_enable', false)) {
            return;
        }
        $this->showDisabledNotice();
    }

    /**
     * 停用提示页：直接输出并结束，不进入任何路由。
     * 注意此时组件还没初始化，所以不要用 __h()/__url() 之类的框架助手。
     */
    protected function showDisabledNotice(): void
    {
        $file = 'config/DuckPhpSettings.config.php';
        if (!headers_sent()) {
            http_response_code(503);
            header('Content-Type: text/html; charset=utf-8');
        }
        $e = htmlspecialchars($file, ENT_QUOTES);
        echo '<!doctype html><html><head><meta charset="utf-8">'
            . '<title>DuckAdmin 未启用</title></head><body>'
            . '<h1>DuckAdmin 未启用</h1>'
            . '<p><strong>出于安全考虑，DuckAdmin 默认不提供服务。</strong>'
            . '必须在本机配置 <code>' . $e . '</code> 里显式开启：</p>'
            . '<pre>return [' . "\n"
            . '    // ... 其它配置 ...' . "\n"
            . "    'duckadmin_enable' => true,\n"
            . '];</pre>'
            . '<p>开启后本页自动消失；不需要这个后台就保持关闭。</p>'
            . '<hr>'
            . '<p><strong>DuckAdmin is disabled.</strong> For security it serves nothing '
            . 'until you explicitly enable it: set <code>\'duckadmin_enable\' =&gt; true</code> '
            . 'in <code>' . $e . '</code>.</p>'
            . '</body></html>';
        exit;
    }
    protected function loadProviders(): void
    {
        $admin_provider = $this->options['data']['duckadmin_admin_provider'] ?? \DuckAdmin\SingleAdmin\System\SingleAdminApp::class;
        $user_provider = $this->options['data']['duckadmin_user_provider'] ?? \DuckAdmin\DemoUsers\System\DemoUsersApp::class;
        $this->options['app'][$admin_provider]['admin_provider_enable'] = true;
        $this->options['app'][$user_provider]['user_provider_enable'] = true;
    }
    /**
     * 保存首页选中的管理员/用户提供者。
     *
     * @param mixed $admin_provider
     * @param mixed $user_provider
     */
    public function saveProviders($admin_provider, $user_provider)
    {
        $data = [];
        $data['duckadmin_admin_provider'] = \DuckAdmin\SingleAdmin\System\SingleAdminApp::class;
        if (isset($this->options['app'][$admin_provider])){
            $data['duckadmin_admin_provider'] = $admin_provider;
        }
        $data['duckadmin_user_provider'] = \DuckAdmin\DemoUsers\System\DemoUsersApp::class;
        if (isset($this->options['app'][$user_provider])){
            $data['duckadmin_user_provider'] = $user_provider;
        }

        ExtOptionsLoader::_()->saveExtOptions($data);
    }

}
