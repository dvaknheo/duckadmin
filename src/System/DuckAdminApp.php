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
        // 安装页等文案来自各子应用的 config/lang-{语言}.php（语言名要写成 en_US 这种形状）；
        // 想临时看英文：加 ?lang=en-US（或让浏览器发 Accept-Language: en-US）。
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

        // 子应用不再各留一份 error_500。视图名写成**绝对路径**时框架会直接用它：
        // View::getViewFile() → App::getOverrideableFile() 开头就 isAbsPath($file) 原样返回，
        // 所以这里指到根应用的那一份，所有子应用（继承根选项）统一渲染它。
        'error_500' => __DIR__ . '/../../view/_sys/error_500.php',

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

        // 让"改了就立刻生效"：opcache 默认 revalidate_freq=2 秒，开发机上这个窗口会
        // 让人以为改了没生效（实测：改 config/DuckPhpSettings.config.php 后 2 秒内仍是
        // 旧值，第 3 秒才生效，我因此误判过一次安全门禁没生效）。
        // 必须放在 parent::onPrepare() **之前** —— 设置文件正是它里面 loadSetting() 读进来的。
        // 代价：每请求多一次 stat（窗口设 0）+ 重编译那个很小的配置文件；CLI 侧本来
        // 不受影响（opcache.enable_cli=Off）。想保留 opcache 原生行为，删掉这段即可。
        if (function_exists('opcache_invalidate')) {
            ini_set('opcache.revalidate_freq', '0');
            $setting_file = (string)($this->options['setting_file'] ?? 'config/DuckPhpSettings.config.php');
            $setting_full = (strpos($setting_file, '/') === 0)
                ? $setting_file
                : realpath($this->options['path']) . '/' . $setting_file;
            @opcache_invalidate($setting_full, true);
        }

        parent::onPrepare();

        // 安全门禁：设置里没开 duckadmin_enable 就只输出提示页并结束，不进入任何路由。
        // 必须放在 parent::onPrepare() 之后 —— App::onPrepare() 里才 loadSetting()，
        // 放前面的话 _Setting() 读到的是空设置，会把「已开启」也挡掉。
        $this->checkEnabled();
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

        // 覆盖测试时允许用采集标记再覆盖一次（见 applyProviderFlag 的说明与锁）
        $this->applyProviderFlag();
    }

    /**
     * 覆盖测试用的「采集标记」→ 提供者白名单。
     *
     * 值 = [类名, 'admin'|'user']。标记只可能在这里面选，而且必须真的在
     * options['app'] 里，所以它不可能指定到白名单之外的类上去。
     */
    protected const FLAG_PROVIDERS = [
        'admin'   => [\DuckAdmin\Admin\System\AdminApp::class, 'admin'],
        'laravel' => [\DuckAdmin\Admin_LaravelModel\System\LaravelModelApp::class, 'admin'],
        'think'   => [\DuckAdmin\Admin_ThinkPHPModel\System\ThinkPHPModelApp::class, 'admin'],
        'single'  => [\DuckAdmin\SingleAdmin\System\SingleAdminApp::class, 'admin'],
        'demo'    => [\DuckAdmin\DemoUsers\System\DemoUsersApp::class, 'user'],
        'user'    => [\DuckAdmin\User\System\UserApp::class, 'user'],
    ];

    /**
     * 覆盖测试专用：按采集标记（`--flag=` / 请求头 X-DuckCoverage-Flag）选提供者。
     *
     * ★ 锁：**只有本机 settings 里 duckcoverage_enable 为真时才生效**。
     *   原因：这个标记走的是 HTTP 请求头，而它决定「用哪个管理员/用户提供者」——
     *   等于让一个请求头影响认证。生产环境 settings 里没有这个键（或为 false），
     *   于是这个分支根本不会进。这个开关和 onPrepare() 里「是否安装采集器」
     *   是同一个，口径一致。
     *
     * 取值来自 FLAG_PROVIDERS 白名单，且必须真的在 options['app'] 里（且未被禁用）。
     * 多个值用 `+` 或 `,` 分隔，例如：`--flag=laravel+demo`、`--flag=single,user`。
     * 只给一侧也可以；另一侧沿用 data 里已经解析好的提供者。
     */
    protected function applyProviderFlag(): void
    {
        if (empty($this->_Setting('duckcoverage_enable'))) {
            return;
        }
        if (!class_exists(\DuckCoverage\DuckCoverage::class)) {
            return;
        }
        $flag = (string)\DuckCoverage\DuckCoverage::_()->getFlag();
        if ($flag === '' && isset($_SERVER['argv']) && is_array($_SERVER['argv'])) {
            // CLI 侧兜底：duckcoverage 的 doCommand() 解析 --flag 发生在应用 init
            // （也就是本方法所在的位置）**之后**，所以在 CLI 进程里 getFlag() 这时
            // 还是空的 —— 而测试清单正是在 CLI 里生成的（清单靠 *_provider_enable
            // 守卫），不兜这一下就会出现"请求头对了、清单却还是默认提供者"的错位。
            // web 请求没有 argv，永远是走头那条路。
            foreach ($_SERVER['argv'] as $i => $arg) {
                $arg = (string)$arg;
                if (strpos($arg, '--flag=') === 0) {
                    $flag = substr($arg, 7);
                    break;
                }
                if ($arg === '--flag' && isset($_SERVER['argv'][$i + 1])) {
                    $flag = (string)$_SERVER['argv'][$i + 1];
                    break;
                }
            }
        }
        if ($flag === '') {
            return;
        }

        $picked = [];
        foreach (preg_split('/[+,]/', $flag) as $key) {
            $key = strtolower(trim((string)$key));
            if ($key === '' || !isset(static::FLAG_PROVIDERS[$key])) {
                continue;
            }
            [$class, $side] = static::FLAG_PROVIDERS[$key];
            if (is_array($this->options['app'][$class] ?? null)) {
                $picked[$side] = $class;
            }
        }
        if (!$picked) {
            return;
        }

        // 先把白名单里所有提供者的 enable 关掉：同一时刻只能有一个管理员 + 一个用户。
        // 只动数组项 —— 被禁用的子应用是 false，不能往上写键。
        foreach (static::FLAG_PROVIDERS as $item) {
            [$class, $side] = $item;
            if (is_array($this->options['app'][$class] ?? null)) {
                $this->options['app'][$class][$side === 'admin' ? 'admin_provider_enable' : 'user_provider_enable'] = false;
            }
        }

        // 两侧各自生效的提供者：标记指定优先，否则沿用刚刚从 data 解析出来的
        $effective = [
            'admin' => $picked['admin'] ?? ($this->options['data']['duckadmin_admin_provider'] ?? null),
            'user'  => $picked['user'] ?? ($this->options['data']['duckadmin_user_provider'] ?? null),
        ];
        foreach ($effective as $side => $class) {
            if (!is_string($class) || !is_array($this->options['app'][$class] ?? null)) {
                continue;
            }
            $enable_key = $side === 'admin' ? 'admin_provider_enable' : 'user_provider_enable';
            $data_key = $side === 'admin' ? 'duckadmin_admin_provider' : 'duckadmin_user_provider';
            $this->options['app'][$class][$enable_key] = true;
            $this->options['data'][$data_key] = $class;
        }
    }

    /**
     * 安全门禁：必须在配置里显式开启 duckadmin_enable 才提供服务。
     *
     * 为什么缺省是「关」：这是「确认你有意把后台暴露出去」的开关，
     * 取不到就当没开，少一个配置项就少一份风险。
     * 命令行不受影响（bin/cli.php 的 cover / call 等命令照常可用）。
     */
    protected function checkEnabled(): void
    {
        if ($this->_Setting('duckadmin_enable', false)) {
            return;
        }
        if ($this->isCli()) {
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

    /**
     * 保存首页选中的管理员/用户提供者。
     *
     * 只接受本应用 app 列表里的合法类名；不合法或缺失的一侧退回源码默认值
     * （options['data']，即 SingleAdminApp / DemoUsersApp），与 onInit() 的兜底一致。
     *
     * 这样两侧总会得到一个可用的提供者：既不会像最初那样把"没有这两个键的
     * data 段"写进配置（首页会 500，而首页正是唯一能改回来的入口），
     * 也不会残留一个已被改坏的旧值。
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

        $data = [];
        foreach ($input as $key => $value) {
            if (is_string($value) && isset($this->options['app'][$value])) {
                $data[$key] = $value;
                continue;
            }
            // 不合法/没带：退回源码默认值
            $default = $this->source_data[$key] ?? null;
            if (is_string($default) && isset($this->options['app'][$default])) {
                $data[$key] = $default;
            }
        }
        if (count($data) !== count($input)) {
            // 连源码默认值都不可用（例如对应的子应用被禁用）：不写，避免写坏配置
            return;
        }

        ExtOptionsLoader::_()->saveExtOptions($data);
    }

}
