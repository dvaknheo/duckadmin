<?php declare(strict_types=1);
namespace CgbAIFormater\System;

use DuckPhp\Foundation\Helper;
use DuckPhp\Foundation\SingletonTrait;
use DuckAdmin\Admin\System\AdminApp;

/**
 * AdminApp（myadmin）子应用 DuckCoverage 测试列表（--replay 用）
 * 流程：删除测试库 → GET 安装页 → POST 安装（runtime/admin-duckcoverage.db，创建默认管理员）→ 管理员登录 → 管理页
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

    public function getTestList()
    {

        $list = <<<EOT
#PHASE_BEGIN
CALL {static}::BeforeTest
WEB install
WEB install driver=sqlite&database[file]=runtime/{test_db}&admin_name={admin}&admin_password={password}&admin_password_confirm={password}
WEB Login/login
WEB Login/login username={admin}&password={password}
#PHASE_END

EOT;
        $args = [
            'test_db' => self::TEST_DB,
            'admin' => self::ADMIN_NAME,
            'password' => self::ADMIN_PASSWORD,
        ];
        $list = str_replace(array_map(fn($k) => '{' . $k . '}', array_keys($args)), array_values($args), $list);

        return $list;
    }
}
