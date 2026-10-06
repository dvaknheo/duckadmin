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

    /** 标记里的短名 → 应用类。与 DuckAdminApp::FLAG_PROVIDERS 保持一致 */
    private const PROVIDERS = [
        'admin'   => \DuckAdmin\Admin\System\AdminApp::class,
        'laravel' => \DuckAdmin\Admin_LaravelModel\System\LaravelModelApp::class,
        'think'   => \DuckAdmin\Admin_ThinkPHPModel\System\ThinkPHPModelApp::class,
        'single'  => \DuckAdmin\SingleAdmin\System\SingleAdminApp::class,
        'demo'    => \DuckAdmin\DemoUsers\System\DemoUsersApp::class,
        'user'    => \DuckAdmin\User\System\UserApp::class,
    ];

    /** 本次实际生效的管理员提供者类名（先看采集标记,标记没有就回落到根应用配置） */
    public static function adminProvider(): string
    {
        $parts = explode('+', static::flag());
        return static::PROVIDERS[$parts[0] ?? ''] ?? (string)(App::Root()->options['data']['duckadmin_admin_provider'] ?? '');
    }

    /** 本次实际生效的用户提供者类名 */
    public static function userProvider(): string
    {
        $parts = explode('+', static::flag());
        return static::PROVIDERS[$parts[1] ?? ''] ?? (string)(App::Root()->options['data']['duckadmin_user_provider'] ?? '');
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

    /**
     * 一次性取出并**清掉**「本次要哪种清单」的参数。
     *
     * 约定（调用方设置、被调用方消费）：
     *   - 调用方在把某个应用 embed 进来之前，往**那个应用**的 options 里塞
     *     ['duckcoverage_test_lister_parameter' => 'short'|'logout']；
     *   - 该应用的清单入口读到就清除，并改返回 short（只建立登录态）或 logout 清单。
     * 之所以是一次性：duckcoverage 在取每个子清单前会 toThisChild()，
     * 此时 App::_() 就是那个子应用，读到的正是塞给它的 options；读完即清，
     * 免得同一次 play 里后续再次包含该应用时被误当成 short。
     */
    public static function takeListerCommand(): string
    {
        $key = 'duckcoverage_test_lister_parameter';
        $app = App::_();
        $cmd = trim((string)($app->options[$key] ?? ''));
        if ($cmd !== '') {
            $app->options[$key] = '';
        }
        return $cmd;
    }

    /** 调用方用：让某个应用的清单「只做登录准备」(short) 或「登出」(logout) */
    public static function wantListOf(string $appClass, string $command = 'short'): void
    {
        $appClass::_()->options['duckcoverage_test_lister_parameter'] = $command;
    }

    /** @var array<string,array{phase:string,body:string}> 顶层预取到的「只登录」清单 */
    protected static $short_cache = [];

    /**
     * ★ 顶层预取：把「管理员/用户提供者」的只登录清单各取一份缓存起来。
     *
     * 必须由**根应用**的 lister 在最顶层调用 —— 只有那里 App::_() 还是根，
     * toThisChild() 才真的把 phase 切到子应用；在子应用自己的 lister 里再切是无效的
     * （组件被绑在它自己的 phase 上，实测报 "Using $this when not in object context"）。
     */
    public static function primeShortLists(): void
    {
        foreach ([static::adminProvider(), static::userProvider()] as $appClass) {
            if ($appClass === '' || isset(static::$short_cache[$appClass])) {
                continue;
            }
            $here = App::Phase();
            App::_()->toThisChild($appClass);
            $there = App::Phase();

            // 在该应用自己的 phase 里塞一次性参数,再用它自己声明的 lister 回调取清单
            // （App 类本身没有 GetTestList,那是各应用 lister 类的方法）。
            App::_()->options['duckcoverage_test_lister_parameter'] = 'short';
            $callback = App::_()->options['duckcoverage_test_lister'] ?? null;
            $body = $callback ? trim((string)($callback)()) : '';
            App::Phase($here);

            $body = trim(str_replace(['#PHASE_BEGIN', '#PHASE_END'], '', $body));
            static::$short_cache[$appClass] = ['phase' => $there, 'body' => $body];
        }
    }

    /**
     * 把某个应用的「只登录」清单**内联**进当前清单：显式包上 PHASE 切换行（打完再切回 $here）。
     *
     * 纯文本拼接 —— 不切 phase、不再调用别的 lister，所以可以在任何应用、任何位置反复内联；
     * 也正因为 PHASE 行是显式的，对方的相对 uri 才会落到它自己的前缀上。
     */
    public static function shortInline(string $appClass, string $here): string
    {
        $item = static::$short_cache[$appClass] ?? null;
        if ($item === null || $item['body'] === '') {
            return "COMMENT {$appClass} 没有可用的短清单(primeShortLists() 未取到,或它不是本次的提供者)";
        }
        return "PHASE {$item['phase']}\n{$item['body']}\nPHASE {$here}";
    }
}//@codeCoverageIgnoreEnd
