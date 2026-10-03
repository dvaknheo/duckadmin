<?php declare(strict_types=1);
/**
 * DuckPhp
 * From this time, you never be alone~
 */
namespace DuckAdmin\System;

use DuckPhp\Foundation\SingletonTrait;

/**
 * 根应用的 DuckCoverage 测试列表
 *
 * 关键点:
 *  - 根应用的 phase 名是空串(''),`#PHASE_BEGIN` 在这里只会生成一个空参数的 `PHASE` 行,
 *    所以根列表不自己发 PHASE 帧,只发 `#INCLUDE_CHILD`。
 *  - `#INCLUDE_CHILD X` 会切到子应用 X 的 phase,再取该子应用的 duckcoverage_test_lister
 *    (TestListerHelper::getChildList),这样:
 *      * 子列表里的 `#PHASE_BEGIN` 才是非空的 phase 名(如 :DuckAdmin / :Admin_LaravelModel)
 *      * 子列表里的 `WEB install` / `WEB login` 等相对 uri 才会带上该子应用的 controller_url_prefix
 *  - 三个 Admin 变体(Admin / Admin_LaravelModel / Admin_ThinkPHPModel)的 lister 是同一个,
 *    只是 phase 不同,所以 `__url()` 会分别解析成 /admin/ 、/admin_laravel/ 、/admin_thinkphp/ 。
 */
class TestLister
{
    use SingletonTrait;

    public static function GetTestList()
    {
        return static::_()->_GetTestList();
    }
    public function _GetTestList()
    {
        $list = [];
        // 要纳入覆盖测试的子应用,按需增删
        $list[] = "#INCLUDE_CHILD DuckAdmin\\Admin\\System\\AdminApp";
        $list[] = "#INCLUDE_CHILD DuckAdmin\\Admin_LaravelModel\\System\\LaravelModelApp";
        $list[] = "#INCLUDE_CHILD DuckAdmin\\Admin_ThinkPHPModel\\System\\ThinkPHPModelApp";
        $list[] = "#INCLUDE_CHILD DuckAdmin\\SingleAdmin\\System\\SingleAdminApp";
        $list[] = "#INCLUDE_CHILD DuckAdmin\\DemoUsers\\System\\DemoUsersApp";
        $list[] = "#INCLUDE_CHILD DuckAdmin\\User\\System\\UserApp";
        // SimpleBlog 还没登记 duckcoverage_test_lister,等第二阶段再接
        // $list[] = "#INCLUDE_CHILD DuckAdmin\\SimpleBlog\\System\\SimpleBlogApp";

        return implode("\n", $list);
    }
}
