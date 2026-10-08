<?php declare(strict_types=1);
namespace DuckAdmin\Admin\System;

use DuckAdmin\System\DuckAdminApp;
use DuckCoverage\TestListWithAuthBase;
use DuckPhp\Core\App;
/**
 * AdminApp 子应用 DuckCoverage 测试列表
 */
//@codeCoverageIgnoreStart
class TestLister extends TestListWithAuthBase
{
    public function _GetTestListForClean(): string
    {
        return '';
    }
    public function _GetTestListForLogin(): string
    {
        $phase = App::Phase();
        $dbFile = DuckAdminApp::_()->getTestDbFile('admin');

        $list = <<<EOT
PHASE {$phase}
WEB install driver=sqlite&database[file]={$dbFile}&admin_name=admin&admin_password=123456&admin_password_confirm=123456
WEB login username=admin&password=123456

EOT;
        return $list;
    }
    public function _GetTestListForLogout(): string
    {
        $phase = App::Phase();
        $list = <<<EOT
PHASE {$phase}
WEB logout
EOT;
        return $list;
    }
    public function _GetTestListFull(): string
    {
        $phase = App::Phase();
        $dbFile = DuckAdminApp::_()->getTestDbFile('admin');
        //$static = static::class;

        $list = <<<EOT
PHASE {$phase}
WEB install
WEB install driver=sqlite&database[file]={$dbFile}&admin_name=admin&admin_password=123456&admin_password_confirm=123456
WEB login
WEB login username=admin&password=123456
WEB index

COMMENT ========== 登录失败分支 ==========
COMMENT MainController->login() 自己 catch 后回显错误,所以这些不会变成 500、也不会脏日志;
COMMENT 覆盖的是 AppBusiness->login() 里的几个 ThrowOn(空值/用户不存在/密码错/账号禁用)
WEB login username=&password=
WEB login username=ghost_user&password=123456
WEB login username=admin&password=wrong_password
WEB login username=disabled_user&password=123456
COMMENT 上面几次失败会把会话换掉,重新登录回超管再继续
WEB login username=admin&password=123456
COMMENT ========== Home 模块 ==========
WEB Home/index
WEB Home/profile
WEB Home/profile realname=Admin&email=admin@test.com&password=
WEB Home/profile password=123456
WEB Home/permissions
WEB Home/menu

COMMENT ========== Admin 模块 ==========
WEB Admin/index
WEB Admin/index username=admin
WEB Admin/create
WEB Admin/save username=testuser1&password=123456&realname=TestUser1&email=test1@test.com&status=1&role_id=1
WEB Admin/edit id=1
WEB Admin/update id=1&username=admin&password=&realname=AdminUpdated&email=admin@updated.com&status=1&role_id=1
WEB Admin/save username=deluser&password=123456&realname=DelUser&email=del@test.com&status=1&role_id=2
WEB Admin/save username=disabled_user&password=123456&realname=Disabled&email=disabled@test.com&status=0&role_id=2
WEB Admin/delete id=999

COMMENT ========== Role 模块 ==========
WEB Role/index
WEB Role/index search=admin
WEB Role/create
WEB Role/save name=testrole&description=testrole&pid=0
WEB Role/edit id=2
WEB Role/update id=2&name=updatedrole&description=updated&pid=0
WEB Role/delete id=999

COMMENT ========== Permission 模块 ==========
WEB Permission/index
WEB Permission/index id=1
WEB Permission/index id=2

COMMENT ========== System 模块 ==========
WEB System/menu
WEB System/menu_scan
WEB System/menu_create
WEB System/menu_save name=testmenu&url=/test&type=1&parent_id=0&weight=0
WEB System/menu_edit id=1
WEB System/menu_update id=1&name=updatedmenu&url=/updated&type=1&parent_id=0&weight=0
WEB System/menu_delete id=999
WEB System/info
WEB System/setting
WEB System/log

COMMENT ========== 非超管路径 ==========
COMMENT deluser 是普通角色(role_id=2):登录后访问页面会走 checkUserUrl() 里的非超管分支,
COMMENT 首页/菜单还会走 menu() 里「按角色取菜单」的分支(超管走的是另一条)
WEB login username=deluser&password=123456
WEB index
WEB Home/index
WEB Home/menu
WEB Home/permissions
WEB Role/index
WEB Admin/index
WEB System/info
WEB logout

EOT;
        return $list;
    }
}//@codeCoverageIgnoreEnd
