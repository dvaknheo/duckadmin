<?php declare(strict_types=1);
/**
 * DuckPhp
 * From this time, you never be alone~
 */
namespace DuckAdmin\System;

use DuckPhp\Foundation\SingletonTrait;

//@codeCoverageIgnoreStart
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
        // ★ 顶层预取：各提供者的「只登录」清单必须先在这里(根这一层)取好。
        // 子应用的 lister 里切 phase 是无效的(组件绑在自己的 phase 上),所以只能根来做;
        // 之后子应用用 CoverageGroup::shortInline() 纯文本内联即可。
        \DuckAdmin\System\CoverageGroup::primeShortLists();
        // 要纳入覆盖测试的子应用,按需增删
        $list[] = "#INCLUDE_CHILD DuckAdmin\\Admin\\System\\AdminApp";
        $list[] = "#INCLUDE_CHILD DuckAdmin\\Admin_LaravelModel\\System\\LaravelModelApp";
        $list[] = "#INCLUDE_CHILD DuckAdmin\\Admin_ThinkPHPModel\\System\\ThinkPHPModelApp";
        $list[] = "#INCLUDE_CHILD DuckAdmin\\SingleAdmin\\System\\SingleAdminApp";
        $list[] = "#INCLUDE_CHILD DuckAdmin\\DemoUsers\\System\\DemoUsersApp";
        $list[] = "#INCLUDE_CHILD DuckAdmin\\User\\System\\UserApp";
        $list[] = "#INCLUDE_CHILD DuckAdmin\\SimpleBlog\\System\\SimpleBlogApp";

        return implode("\n", $list);
    }
}//@codeCoverageIgnoreEnd
