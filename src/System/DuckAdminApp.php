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

    /**
     * 源码里提供者默认值的快照（在 onPrepare 里存）。
     * 动态配置文件缺失或被写坏时用它兜底，见 onInit()。
     * @var array<string, mixed>
     */
    protected $source_data = [];

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

        // 快照源码里的提供者默认值：initComponents() 之后 options['data'] 会被
        // 动态配置文件的内容整体替换，那时就读不到源码默认值了，而 onInit() 要用它兜底。
        $this->source_data = (array)($this->options['data'] ?? []);
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
        
        // 选中哪个提供者由 data(动态配置文件)决定；这份配置可能缺失、也可能被写坏，
        // 所以取不到合法类名时回退到源码默认值 —— 否则首页会因为
        // "No GlobalAdmin Provider / No GlobalUser Provider" 直接 500，
        // 而首页正是唯一能改回来的入口(死锁)。
        $providers = [
            'duckadmin_admin_provider' => 'admin_provider_enable',
            'duckadmin_user_provider' => 'user_provider_enable',
        ];
        foreach ($providers as $key => $flag) {
            $provider = $this->options['data'][$key] ?? null;
            if (!is_string($provider) || !isset($this->options['app'][$provider])) {
                $provider = $this->source_data[$key] ?? null;
            }
            if (!is_string($provider) || !isset($this->options['app'][$provider])) {
                continue;
            }
            $this->options['app'][$provider][$flag] = true;
            // 把生效值写回 data，首页表单才能显示"当前选中项"
            $this->options['data'][$key] = $provider;
        }
    }

    /**
     * 保存首页选中的管理员/用户提供者。
     *
     * 只接受本应用 app 列表里的合法类名：非法或缺失的一侧**保留原值**；
     * 两侧都凑不出合法值时不写文件 —— 否则会把一个"没有这两个键的 data 段"
     * 写进配置，之后首页会因为 No GlobalAdmin/GlobalUser Provider 直接 500，
     * 而首页正是唯一能改回来的入口(死锁)。
     *
     * @param mixed $admin_provider
     * @param mixed $user_provider
     */
    public function saveProviders($admin_provider, $user_provider)
    {
        $input = [
            'duckadmin_admin_provider' => $admin_provider,
            'duckadmin_user_provider' => $user_provider,
        ];
        $old = (array)($this->options['data'] ?? []);

        $data = [];
        foreach ($input as $key => $value) {
            if (is_string($value) && isset($this->options['app'][$value])) {
                $data[$key] = $value;
            } elseif (isset($old[$key]) && is_string($old[$key]) && isset($this->options['app'][$old[$key]])) {
                $data[$key] = $old[$key];
            }
        }
        if (count($data) !== count($input)) {
            // 两侧都拿不到合法值(表单没带字段等)：不写，避免写坏配置文件
            return;
        }

        ExtOptionsLoader::_()->saveExtOptions($data);
    }

}
