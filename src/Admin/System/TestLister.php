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
        $id = @file_get_contents(Helper::PathOfRuntime().'DuckCoverage/DuckCoverage.watching.txt');
        @unlink("runtime/DuckCoverage/{$id}.db");
        $dbFile = urlencode("runtime/DuckCoverage/{$id}.db");
        $list = [];

        // ========== Phase 1: 初始化阶段（必须）==========
        $list[] = '#PHASE_BEGIN';
        $list[] = 'CALL {static}::BeforeTest';
        $list[] = 'WEB install';
        $list[] = "WEB install driver=sqlite&database[file]={$dbFile}&admin_name=" . self::ADMIN_NAME . "&admin_password=" . self::ADMIN_PASSWORD . "&admin_password_confirm=" . self::ADMIN_PASSWORD;
        $list[] = 'WEB login';
        $list[] = "WEB login username=" . self::ADMIN_NAME . "&password=" . self::ADMIN_PASSWORD;
        $list[] = 'WEB index';
        $list[] = '#PHASE_END';

        // ========== Phase 2: Home 模块 ==========
        $list[] = '#PHASE_BEGIN';
        $list[] = 'WEB Home/index';
        $list[] = 'WEB Home/profile';
        $list[] = 'WEB Home/profile realname=Admin&email=admin@test.com&password=';
        $list[] = 'WEB Home/permissions';
        $list[] = 'WEB Home/menu';
        $list[] = '#PHASE_END';

        // ========== Phase 3: Admin 模块 ==========
        $list[] = '#PHASE_BEGIN';
        $list[] = 'WEB Admin/index';
        $list[] = 'WEB Admin/create';
        $list[] = 'WEB Admin/save username=testuser&password=123456&realname=TestUser&email=test@test.com&status=1&role_id=1';
        $list[] = 'WEB Admin/edit id=1';
        $list[] = 'WEB Admin/update id=1&username=admin&password=&realname=AdminUpdated&email=admin@updated.com&status=1&role_id=1';
        $list[] = 'WEB Admin/save username=deluser&password=123456&realname=DelUser&email=del@test.com&status=1&role_id=2';
        $list[] = 'WEB Admin/delete id=999';
        $list[] = '#PHASE_END';

        // ========== Phase 4: Role 模块 ==========
        $list[] = '#PHASE_BEGIN';
        $list[] = 'WEB Role/index';
        $list[] = 'WEB Role/create';
        $list[] = 'WEB Role/save name=testrole&description=testrole&pid=0';
        $list[] = 'WEB Role/edit id=1';
        $list[] = 'WEB Role/update id=2&name=updatedrole&description=updated&pid=0';
        $list[] = 'WEB Role/delete id=999';
        $list[] = '#PHASE_END';

        // ========== Phase 5: Permission 模块 ==========
        $list[] = '#PHASE_BEGIN';
        $list[] = 'WEB Permission/index';
        $list[] = 'WEB Permission/index id=1';
        $list[] = '#PHASE_END';

        // ========== Phase 6: System 模块 ==========
        $list[] = '#PHASE_BEGIN';
        $list[] = 'WEB System/menu';
        $list[] = 'WEB System/menu_scan';
        $list[] = 'WEB System/menu_create';
        $list[] = 'WEB System/menu_save name=testmenu&url=/test&type=1&parent_id=0&weight=0';
        $list[] = 'WEB System/menu_edit id=1';
        $list[] = 'WEB System/menu_update id=1&name=updatedmenu&url=/updated&type=1&parent_id=0&weight=0';
        $list[] = 'WEB System/menu_delete id=999';
        $list[] = '#PHASE_END';

        $str = implode("\n", $list);

        $args = [
            'static' => static::class,
        ];
        $str = str_replace(array_map(fn($k) => '{' . $k . '}', array_keys($args)), array_values($args), $str);

        return $str;
    }
}
