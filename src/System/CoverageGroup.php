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
}
