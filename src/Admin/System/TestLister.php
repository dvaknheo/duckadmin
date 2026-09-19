<?php declare(strict_types=1);
namespace DuckAdmin\Admin\System;

use DuckPhp\Foundation\Helper;
use DuckPhp\Foundation\SingletonTrait;

/**
 * AdminApp 子应用 DuckCoverage 测试列表
 */
class TestLister
{
    use SingletonTrait;

    public const TEST_DB = 'admin-duckcoverage.db';
    public const ADMIN_NAME = 'admin';
    public const ADMIN_PASSWORD = 'adminadmin';

    public static function BeforeTest()
    {
        static::_()->_BeforeTest();
    }

    public function _BeforeTest()
    {
        @unlink(Helper::PathOfRuntime() . self::TEST_DB);
    }

    public static function GetTestList()
    {
        return static::_()->_GetTestList();
    }

    public function _GetTestList()
    {
        $dbFile = 'runtime/' . self::TEST_DB;
        $list = [];
        $list[] = '#PHASE_BEGIN';
        $list[] = 'CALL {static}::BeforeTest';
        $list[] = 'WEB install';
        $list[] = "WEB install driver=sqlite&database[file]={$dbFile}&admin_name=" . self::ADMIN_NAME . "&admin_password=" . self::ADMIN_PASSWORD . "&admin_password_confirm=" . self::ADMIN_PASSWORD;
        $list[] = 'WEB login';
        $list[] = "WEB login username=" . self::ADMIN_NAME . "&password=" . self::ADMIN_PASSWORD;
        $list[] = '#PHASE_END';

        // Home 模块
        $list[] = 'WEB Home/index';
        $list[] = 'WEB Home/profile';
        $list[] = 'WEB Home/permissions';
        $list[] = 'WEB Home/menu';

        // Admin 模块
        $list[] = 'WEB Admin/index';
        $list[] = 'WEB Admin/create';
        $list[] = 'WEB Admin/edit id=1';

        // Role 模块
        $list[] = 'WEB Role/index';
        $list[] = 'WEB Role/create';
        $list[] = 'WEB Role/edit id=1';

        // Permission 模块
        $list[] = 'WEB Permission/index';
        $list[] = 'WEB Permission/index id=1';

        // System 模块
        $list[] = 'WEB System/menu';

        $str = implode("\n", $list);

        $args = [
            'static' => static::class,
        ];
        $str = str_replace(array_map(fn($k) => '{' . $k . '}', array_keys($args)), array_values($args), $str);

        return $str;
    }
}
