<?php declare(strict_types=1);
/**
 * DuckAdmin DemoUsers - 预设用户系统
 * 不使用数据库、不使用安装系统;登录限定 demo_users 数组中的用户
 */
namespace DuckAdmin\DemoUsers\System;

use DuckPhp\Foundation\SingletonTrait;


class TestLister
{
    use SingletonTrait;
    public static function GetTestOrderList(): string
    {
        return static::_()->_GetTestOrderList();
    }
    public static function _GetTestOrderList(): string
    {
        $list = '';
        return $list;
    }

}