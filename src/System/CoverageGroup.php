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



}//@codeCoverageIgnoreEnd
