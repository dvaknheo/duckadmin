<?php declare(strict_types=1);
namespace DuckAdmin\Admin\System;

use DuckPhp\Foundation\Helper;
use DuckPhp\Foundation\SingletonTrait;

/**
 * AdminApp 子应用 DuckCoverage 测试列表
 * 流程：删除测试库 → 安装 → 登录 → 各模块测试
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
        // 删除测试库文件，从干净状态开始
        @unlink(Helper::PathOfRuntime() . self::TEST_DB);
    }

    public static function GetTestList()
    {
        return static::_()->_GetTestList();
    }
    public function _GetTestList()
    {
        $list = <<<EOT
#PHASE_BEGIN
CALL {static}::BeforeTest
WEB install
WEB install driver=sqlite&database[file]=runtime/{test_db}&admin_name={admin}&admin_password={password}&admin_password_confirm={password}
WEB login
WEB login username={admin}&password={password}
#PHASE_END

EOT;
        $args = [
            'test_db' => self::TEST_DB,
            'admin' => self::ADMIN_NAME,
            'password' => self::ADMIN_PASSWORD,
            'static' => static::class,
        ];
        $list = str_replace(array_map(fn($k) => '{' . $k . '}', array_keys($args)), array_values($args), $list);
        return $list;
    }
}
