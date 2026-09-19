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

    public const TEST_DB = 'admin-ZZZ.db';
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
        $id = @file_get_contents( Helper::PathOfRuntime().'DuckCoverage/DuckCoverage.watching.txt');
        @unlink("runtime/DuckCoverage/{$id}.db");
        $dbFile = urlencode("runtime/DuckCoverage/{$id}.db");
        $list = [];

        // ========== 初始化阶段（必须）==========
        $list[] = '#PHASE_BEGIN';
        $list[] = 'CALL {static}::BeforeTest';
        $list[] = 'WEB install';
        $list[] = "WEB install driver=sqlite&database[file]={$dbFile}&admin_name=" . self::ADMIN_NAME . "&admin_password=" . self::ADMIN_PASSWORD . "&admin_password_confirm=" . self::ADMIN_PASSWORD;
        $list[] = 'WEB login';
        $list[] = "WEB login username=" . self::ADMIN_NAME . "&password=" . self::ADMIN_PASSWORD;
        $list[] = 'WEB index';

        // ========== Home 模块 ==========
        $list[] = 'WEB Home/index';
        // $list[] = 'WEB Home/profile';
        // $list[] = 'WEB Home/permissions';
        // $list[] = 'WEB Home/menu';

        // // Home/profile POST - 更新个人信息
        // $list[] = 'WEB Home/profile realname=Admin&email=admin@test.com&password=';

        // // ========== Admin 模块 ==========
        // $list[] = 'WEB Admin/index';
        // $list[] = 'WEB Admin/create';
        // // Admin/save POST - 创建用户
        // $list[] = 'WEB Admin/save username=testuser&password=123456&realname=TestUser&email=test@test.com&status=1&role_id=1';
        // // Admin/edit GET
        // $list[] = 'WEB Admin/edit id=1';
        // // Admin/update POST - 更新用户
        // $list[] = 'WEB Admin/update id=1&username=admin&password=&realname=AdminUpdated&email=admin@updated.com&status=1&role_id=1';
        // // Admin/delete GET - 注意：删除当前用户会导致后续session失效，所以用非超管用户测试
        // // 先创建一个用户再删除
        // $list[] = 'WEB Admin/save username=deluser&password=123456&realname=DelUser&email=del@test.com&status=1&role_id=2';
        // // 删除刚创建的用户
        // $list[] = 'WEB Admin/delete id=999'; // 删除不存在的ID，避免影响

        // // ========== Role 模块 ==========
        // $list[] = 'WEB Role/index';
        // $list[] = 'WEB Role/create';
        // // Role/save POST - 创建职位
        // $list[] = 'WEB Role/save name=testrole&description=testrole&pid=0';
        // // Role/edit GET
        // $list[] = 'WEB Role/edit id=1';
        // // Role/update POST - 更新职位
        // $list[] = 'WEB Role/update id=2&name=updatedrole&description=updated&pid=0';
        // // Role/delete GET
        // $list[] = 'WEB Role/delete id=999'; // 删除不存在的ID

        // // ========== Permission 模块 ==========
        // $list[] = 'WEB Permission/index';
        // $list[] = 'WEB Permission/index id=1';

        // // ========== System 模块 ==========
        // $list[] = 'WEB System/menu';
        // $list[] = 'WEB System/menu_scan';
        // $list[] = 'WEB System/menu_create';
        // // System/menu_save POST - 创建菜单
        // $list[] = 'WEB System/menu_save name=testmenu&url=/test&type=1&parent_id=0&weight=0';
        // // System/menu_edit GET
        // $list[] = 'WEB System/menu_edit id=1';
        // // System/menu_update POST - 更新菜单
        // $list[] = 'WEB System/menu_update id=1&name=updatedmenu&url=/updated&type=1&parent_id=0&weight=0';
        // // System/menu_delete GET
        // $list[] = 'WEB System/menu_delete id=999'; // 删除不存在的ID

        $list[] = '#PHASE_END';

        $str = implode("\n", $list);

        $args = [
            'static' => static::class,
        ];
        $str = str_replace(array_map(fn($k) => '{' . $k . '}', array_keys($args)), array_values($args), $str);

        return $str;
    }
}
