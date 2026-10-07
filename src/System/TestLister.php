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
        $list[] = "#INCLUDE_CHILD DuckAdmin\\Admin\\System\\AdminApp";
        $list[] = "PHASE ";
        $list[] = "#INCLUDE_CHILD DuckAdmin\\Admin_LaravelModel\\System\\LaravelModelApp";
        $list[] = "PHASE ";
        $list[] = "#INCLUDE_CHILD DuckAdmin\\Admin_ThinkPHPModel\\System\\ThinkPHPModelApp";
        $list[] = "PHASE ";
        $list[] = "#INCLUDE_CHILD DuckAdmin\\SingleAdmin\\System\\SingleAdminApp";
        $list[] = "PHASE ";
        $list[] = "#INCLUDE_CHILD DuckAdmin\\DemoUsers\\System\\DemoUsersApp";
        $list[] = "PHASE ";
        $list[] = "#INCLUDE_CHILD DuckAdmin\\User\\System\\UserApp";
        $list[] = "PHASE ";
        $list[] = "#INCLUDE_CHILD DuckAdmin\\SimpleBlog\\System\\SimpleBlogApp";
        $list[] = "PHASE ";

        return implode("\n", $list);
    }
}//@codeCoverageIgnoreEnd
