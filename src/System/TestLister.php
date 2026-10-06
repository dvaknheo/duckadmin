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
