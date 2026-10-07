<?php declare(strict_types=1);
/**
 * DuckAdmin - 覆盖测试「当前组名」的统一解析
 *
 * 为什么需要它：Admin / SimpleBlog / User 三处 TestLister 都要按组名算出本组的 sqlite 库
 * （runtime/DuckCoverage/<组名>.db）。它们原来要么直接读 DuckCoverage.watching.txt，
 * 要么用上游的 DuckCoverage::watchingGetName()，两者都没有 trim、也没有兜底 ——
 * 一旦读到空值（上游在 watch 结束时会把那个文件 unlink 掉），就会拼出
 * runtime/DuckCoverage/.db 这种空组名路径，"安装"落到一个库、"注册/登录"落到另一个库，
 * 于是整片功能看起来就是没被覆盖。实测踩过：SimpleBlog 的用户侧一片 0% 就是这个原因
 * （6 组一起跑时出现，单独跑该组时不出现，非常难查）。
 *
 * 解析顺序：
 *   1. 环境变量 DUCKADMIN_COVERAGE_GROUP —— .ai/cover_flags.sh 每组都会设，最可靠；
 *   2. 上游 DuckCoverage::watchingGetName()（trim 后）；
 *   3. 直接读 runtime/DuckCoverage/DuckCoverage.watching.txt（trim 后）；
 * 三者都拿不到就抛异常：宁可这一组报错，也不要静默地测到错的库上。
 */
namespace DuckAdmin\System;

use DuckPhp\Core\App;

//@codeCoverageIgnoreStart
final class CoverageGroup
{
    /** 当前采集组名 */
    public static function name(): string
    {
        $name = trim((string)(getenv('DUCKADMIN_COVERAGE_GROUP') ?: ''));
        if ($name !== '') {
            return $name;
        }

        if (class_exists(\DuckCoverage\DuckCoverage::class)) {
            $name = trim((string)\DuckCoverage\DuckCoverage::_()->watchingGetName());
            if ($name !== '') {
                return $name;
            }
        }

        $file = App::_()->getRuntimePath() . 'DuckCoverage/DuckCoverage.watching.txt';
        $name = trim((string)@file_get_contents($file));
        if ($name !== '') {
            return $name;
        }

        throw new \RuntimeException(
            'cover: 拿不到当前采集组名(DUCKADMIN_COVERAGE_GROUP 未设置, watching 文件也读不到). '
            . '手跑时请 export DUCKADMIN_COVERAGE_GROUP=<组名>; 用 .ai/cover_flags.sh 跑不会有这个问题.'
        );
    }

    /**
     * 本组 sqlite 库的相对路径（已 urlencode，可直接放进 WEB 的 POST body）
     */
    public static function dbFile(string $prefix = 'runtime/DuckCoverage/'): string
    {
        return urlencode($prefix . self::name() . '.db');
    }

    /**
     * 本次采集标记（.ai/cover_flags.sh 每组 export 的 DUCKADMIN_COVERAGE_FLAG）
     *
     * 为什么 lister 需要它：清单是在 CLI 侧生成的，那时没有 HTTP 请求，也就没有
     * X-DuckCoverage-Flag 头 —— 但清单里要写「管理端登录」「用户端注册」这类**跨应用绝对路由**，
     * 前缀必须与本次实际生效的提供者一致，否则会打到没挂载的路径上（例如 single+demo 组里
     * 写死 /admin/ 的登录会 404，管理端一路登录不上）。
     */
    public static function flag(): string
    {
        return trim((string)(getenv('DUCKADMIN_COVERAGE_FLAG') ?: ''));
    }

    /**
     * 覆盖测试用的「采集标记」→ 提供者白名单。值 = [类名, 'admin'|'user']。
     * 标记只可能在这里面选，而且必须真的在 options['app'] 里，所以不可能指到白名单之外。
     */
    public const FLAG_PROVIDERS = [
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
     *   于是这个分支根本不会进。这个开关和 DuckAdminApp::onPrepare() 里
     *   「是否安装采集器」是同一个，口径一致。
     *
     * 取值来自 FLAG_PROVIDERS 白名单，且必须真的在 options['app'] 里（且未被禁用）。
     * 多个值用 `+` 或 `,` 分隔，例如：`--flag=laravel+demo`、`--flag=single,user`。
     * 只给一侧也可以；另一侧沿用 data 里已经解析好的提供者。
     *
     * 本类整文件 @codeCoverageIgnore：这段逻辑只在采集时（settings 开着 + 带标记）才走得到，
     * 属测试工具；放在这里也让根应用少一批"永远不会被真实请求跑到"的未覆盖行。
     */
    public static function applyProviderFlag(): void
    {
        $root = App::Root();
        if (empty($root->_Setting('duckcoverage_enable'))) {
            return;
        }
        if (!class_exists(\DuckCoverage\DuckCoverage::class)) {
            return;
        }
        $flag = (string)\DuckCoverage\DuckCoverage::_()->getFlag();
        if ($flag === '' && isset($_SERVER['argv']) && is_array($_SERVER['argv'])) {
            // CLI 侧兜底：duckcoverage 的 doCommand() 解析 --flag 发生在应用 init
            // （也就是调用本方法的位置）**之后**，所以在 CLI 进程里 getFlag() 这时
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
            if (is_array($root->options['app'][$class] ?? null)) {
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
            if (is_array($root->options['app'][$class] ?? null)) {
                $root->options['app'][$class][$side === 'admin' ? 'admin_provider_enable' : 'user_provider_enable'] = false;
            }
        }

        // 两侧各自生效的提供者：标记指定优先，否则沿用刚刚从 data 解析出来的
        $effective = [
            'admin' => $picked['admin'] ?? ($root->options['data']['duckadmin_admin_provider'] ?? null),
            'user'  => $picked['user'] ?? ($root->options['data']['duckadmin_user_provider'] ?? null),
        ];
        foreach ($effective as $side => $class) {
            if (!is_string($class) || !is_array($root->options['app'][$class] ?? null)) {
                continue;
            }
            $enable_key = $side === 'admin' ? 'admin_provider_enable' : 'user_provider_enable';
            $data_key = $side === 'admin' ? 'duckadmin_admin_provider' : 'duckadmin_user_provider';
            $root->options['app'][$class][$enable_key] = true;
            $root->options['data'][$data_key] = $class;
        }
    }


    /** 本次实际生效的管理员提供者类名（先看采集标记,标记没有就回落到根应用配置） */
    public static function adminProvider(): string
    {
        $parts = explode('+', static::flag());
        return static::FLAG_PROVIDERS[$parts[0] ?? ''][0] ?? (string)(App::Root()->options['data']['duckadmin_admin_provider'] ?? '');
    }

    /** 本次实际生效的用户提供者类名 */
    public static function userProvider(): string
    {
        $parts = explode('+', static::flag());
        return static::FLAG_PROVIDERS[$parts[1] ?? ''][0] ?? (string)(App::Root()->options['data']['duckadmin_user_provider'] ?? '');
    }

    /** 某个提供者应用的 controller_url_prefix（规范成带前导斜杠,如 /admin/、/single_admin/） */
    public static function prefixOf(string $appClass): string
    {
        $prefix = trim((string)(App::Root()->options['app'][$appClass]['controller_url_prefix'] ?? ''));
        // 根配置里写的是 'single_admin/'(没有前导斜杠),直接拼会变成 /blog/single_admin/login
        return '/' . ltrim($prefix, '/');
    }

    /** 管理端前缀：清单里写「跨应用绝对路由」时用它,别写死 /admin/ */
    public static function adminPrefix(): string
    {
        return static::prefixOf(static::adminProvider());
    }

    /** 用户端前缀 */
    public static function userPrefix(): string
    {
        return static::prefixOf(static::userProvider());
    }



}//@codeCoverageIgnoreEnd
