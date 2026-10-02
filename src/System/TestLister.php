<?php declare(strict_types=1);
/**
 * DuckPhp
 * From this time, you never be alone~
 */
namespace DuckAdmin\System;

use DuckPhp\Core\App;
use DuckPhp\Foundation\SingletonTrait;

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
        $list[] ="#PHASE_BEGIN";
        // $list[] ="#INCLUDE_CHILD DuckAdmin\\Admin\\System\\AdminApp";
        // $list[] ="#INCLUDE_CHILD DuckAdmin\\Admin_LaravelModel\\System\\LaravelModelApp";
        // $list[] ="#INCLUDE_CHILD DuckAdmin\\Admin_ThinkPHPModel\\System\\ThinkPHPModelApp";
        // $list[] ="#INCLUDE_CHILD DuckAdmin\\SingleAdmin\\System\\SingleAdminApp";
        // $list[] ="#INCLUDE_CHILD DuckAdmin\\DemoUsers\\System\\DemoUsersApp";
        // $list[] ="#INCLUDE_CHILD DuckAdmin\\User\\System\\UserApp";
        // $list[] ="#INCLUDE_CHILD DuckAdmin\\SimpleBlog\\System\\SimpleBlogApp";
        $list[] ="#PHASE_END";
        $ret = implode("\n",$list);
        return $ret;
    }
   

}