<?php declare(strict_types=1);
namespace DuckAdmin\User\System;

use DuckPhp\Core\App;
use DuckPhp\Foundation\SingletonTrait;

//@codeCoverageIgnoreStart
class TestLister
{
    use SingletonTrait;
    public static function GetTestList(): string
    {
        return static::_()->_GetTestList();
    }
    public static function GetShortTestOrderList(): string
    {
        return static::_()->_GetShortTestOrderList();
    }
    public static function _GetShortTestOrderList(): string
    {
        return '';
    }
    public static function _GetTestList(): string
    {
        $flag = App::_()->options['user_provider_enable'] ?? true;
        if(!$flag){
            return '';
        }
        $list_referenct = <<<EOT
WEB users/Home/index
WEB users/
WEB users/logout
UserBusiness UserBusiness@canAccess user_id=&class=&method=&url=
CALL DuckAdmin\DemoUsers\Business\UserBusiness@log user_id=&string=&type=&ext=
CALL DuckAdmin\DemoUsers\Business\UserBusiness@batchGetUsernames ids=
CALL DuckAdmin\DemoUsers\Business\UserBusiness@login post=

EOT;

        // 管理员登录行：用绝对 uri（跨应用），账号与 Admin 清单保持同一个来源。
        // 用户管理页需要管理员登录态，而管理员态可能在用户侧的操作后被清掉，
        // 所以在那几步之前显式重登一次；否则 AdminControllerBase 会直接 302，
        // AdminController 与 view/Admin/* 永远是 0%。
        $admin_login = 'WEB /admin/login username=' . \DuckAdmin\Admin\System\TestLister::ADMIN_NAME
            . '&password=' . \DuckAdmin\Admin\System\TestLister::ADMIN_PASSWORD;

        $list = <<<EOT
#PHASE_BEGIN
CALL {static}@beginTest
WEB install driver=sqlite&database%5Bfile%5D={db}&database%5Bhost%5D=127.0.0.1&database%5Bport%5D=&database%5Bdbname%5D=&database%5Busername%5D=&database%5Bpassword%5D=&action=install

COMMENT 注册:表单 + 成功 + 重名 + 两次密码不一致
WEB register
WEB register name=t1&password=123456&password_confirm=123456
WEB register name=t1&password=123456&password_confirm=123456
WEB register name=t2&password=123456&password_confirm=999999

COMMENT 登录:表单 + 错密码 + 正确
WEB login
WEB login name=t1&password=nollllllllllllllllllllog
WEB login name=t1&password=123456
WEB index

COMMENT 登录后的主页与改密码页(HomeController 需要登录态)
COMMENT 注意:MainController 没有 password action,要用 Home/password
WEB Home/index
WEB Home/password
WEB Home/password oldpassword=123456&newpassword=654321&newpassword_confirm=654321
WEB Home/password oldpassword=654321&newpassword=123456&newpassword_confirm=123456
WEB Home/password oldpassword=123456&newpassword=123456&newpassword_confirm=999999

COMMENT 用户管理(必须放在用户登出之前:登出会把整个 session 清掉,
COMMENT 管理员登录态也会一起没,AdminControllerBase 就会直接 302 出去,
COMMENT 于是 AdminController 与 view/Admin/* 永远是 0%)
COMMENT 管理员登录态由同一组的 Admin 块负责建立
COMMENT 管理员登录态可能已被用户侧的操作清掉:这里用绝对 uri 重新登录一次
{$admin_login}
WEB Admin/index
WEB Admin/index all=1
WEB Admin/delete id=2
WEB Admin/index all=1
WEB Admin/undelete id=2
WEB Admin/delete id=999
WEB Admin/index

COMMENT 英文语言包:不带 ?lang=en 时 config/lang-en.php 永远不加载,一直是 0%
COMMENT HTTP 表达不到的业务/模型层交给 SETWEB 后置钩子(见下面的 moreTest)
SETWEB _ _ {phase}!{static}@moreTest _
WEB login?lang=en
WEB Home/index?lang=en
WEB logout

CALL {static}@endTest
#PHASE_END

EOT;
        $file = UserApp::_()->getRuntimePath() .'DuckCoverage/DuckCoverage.watching.txt';
        $watch_name = file_get_contents($file);
        $db = "runtime/DuckCoverage/{$watch_name}.db";

        $list = str_replace('{db}', $db, $list);
        $list = str_replace('{static}', static::class, $list);
        $list = str_replace('{phase}', UserApp::Phase(), $list);

        return $list;
    }
    public function beginTest()
    {
        
        //我们要删除旧测试数据库文件
    }
    public function endTest()
    {
        //我们要删除旧测试数据库文件
    }

    /**
     * SETWEB 后置钩子：在每个 web 调用之后触发，用来覆盖 HTTP 表达不到的
     * 业务层/模型层方法。
     *
     * 为什么必须在这里调、而不是用 #BUSINESS/#MODEL 宏展开成 CALL：
     * 宏展开是 `CALL :DuckUser!DuckAdmin\User\Business\...`，在 CLI 里直接执行，
     * 那时子应用的 database_list 还没有生效，模型层一取数据库就抛
     * "setting database_list[0] missing" 并中断整场 play（实测过）。
     * 钩子是在该子应用的真实请求里跑的，配置已生效 —— 与 DemoUsers 的
     * TestLister::moreTest2() 同一套路。
     *
     * 每步各自 try：任何一个失败都不影响其它步，也不会中断 play；
     * static 守卫让这一批调用只跑一次（钩子本身会随每个 web 调用触发）。
     */
    public function moreTest()
    {
        static $done = false;
        if ($done) {
            return;
        }
        $done = true;

        $calls = [
            // UserAdminBusiness / UserAdminModel：整文件原来 0%
            ['\DuckAdmin\User\Business\UserAdminBusiness', 'getUserList', [[], 1, 10]],
            ['\DuckAdmin\User\Business\UserAdminBusiness', 'getUserList', [['all' => 1], 1, 10]],
            ['\DuckAdmin\User\Business\UserAdminBusiness', 'deleteUser', [1, 2]],
            ['\DuckAdmin\User\Business\UserAdminBusiness', 'unDeleteUser', [1, 2]],
            ['\DuckAdmin\User\Model\UserAdminModel', 'getUserList', [[], 1, 5]],
            ['\DuckAdmin\User\Model\UserAdminModel', 'getUserList', [['all' => 1], 1, 5]],
            ['\DuckAdmin\User\Model\UserAdminModel', 'deleteUser', [2]],
            ['\DuckAdmin\User\Model\UserAdminModel', 'unDeleteUser', [2]],
            // UserModel / UserBusiness 剩下的分支
            ['\DuckAdmin\User\Model\UserModel', 'getUserById', [1]],
            ['\DuckAdmin\User\Model\UserModel', 'getUserByUsername', ['t1']],
            ['\DuckAdmin\User\Model\UserModel', 'exsits', ['nobody_xyz']],
            ['\DuckAdmin\User\Model\UserModel', 'addUser', ['t9', '123456']],
            ['\DuckAdmin\User\Model\UserModel', 'updatePassword', [1, '123456']],
            ['\DuckAdmin\User\Model\UserModel', 'batchGetUsernames', [[1, 2]]],
            ['\DuckAdmin\User\Business\UserBusiness', 'batchGetUsernames', [[1, 2]]],
            ['\DuckAdmin\User\Business\UserBusiness', 'changePassword', [1, '123456', '123456']],
            ['\DuckAdmin\User\Business\UserBusiness', 'log', [1, 'coverage', 'test']],
        ];
        foreach ($calls as [$class, $method, $args]) {
            try {
                $class::_()->$method(...$args);
            } catch (\Throwable $ex) {
                // 这里就是来踩边界的：失败无所谓，不能让它中断整场 play
            }
        }
    }

}
/*
// 首先，我们要搞安装系统
我们从注册登录 安装开始

$list = <<<EOT
COMMENT tests for x
WEB index
WEB register
WEB register name={username}&password=123456&password_confirm=123456
WEB register name={username}&ssssssssssssssssssssssamename=1
WEB Home/index?_r=1
WEB logout
WEB index
WEB login
WEB login name={username}&password=nolllllllllllllllllllogin
WEB login name={username}&password=123456
WEB Home/index?_r=2
WEB Home/password
WEB Home/password oldpassword=123456&newpassword=654321&newpassword_confirm=654321
WEB Home/password oldpassword=654321&newpassword=123456&newpassword_confirm=123456
WEB Home/password oldpassword=654321&newpassword=123456&newpassword_confirm=123456
EOT;
$list = <<<EOT
PHASE {phase}
WEB register
EOT;


RUN DuckUser:version
RUN DuckUser:help
RUN DuckUser:run
RUN DuckUser:fetch
RUN DuckUser:call
RUN DuckUser:routes
RUN DuckUser:debug
WEB user/Admin/index
WEB user/Admin/delete
WEB user/Admin/undelete
WEB user/Home/index
WEB user/Home/password
WEB user/
WEB user/register
WEB user/login
WEB user/logout
CALL DuckAdmin\User\Business\UserAdminBusiness@getUserList conditions=&page=1&page_size=10
CALL DuckAdmin\User\Business\UserAdminBusiness@deleteUser admin_id=&id=
CALL DuckAdmin\User\Business\UserAdminBusiness@unDeleteUser admin_id=&id=
CALL DuckAdmin\User\Business\UserBusiness@log user_id=&string=&type=
CALL DuckAdmin\User\Business\UserBusiness@checkAccess id=&class=&method=&url=
CALL DuckAdmin\User\Business\UserBusiness@register form=
CALL DuckAdmin\User\Business\UserBusiness@login form=
CALL DuckAdmin\User\Business\UserBusiness@changePassword uid=&password=&new_password=
CALL DuckAdmin\User\Business\UserBusiness@batchGetUsernames ids=
CALL DuckAdmin\User\Business\UserBusiness@getUserList
CALL DuckAdmin\User\Model\UserAdminModel@getUserList where=&page=1&page_size=10
CALL DuckAdmin\User\Model\UserAdminModel@deleteUser id=
CALL DuckAdmin\User\Model\UserAdminModel@unDeleteUser id=
CALL DuckAdmin\User\Model\UserModel@init
CALL DuckAdmin\User\Model\UserModel@exsits name=
CALL DuckAdmin\User\Model\UserModel@addUser username=&password=
CALL DuckAdmin\User\Model\UserModel@getUserById id=
CALL DuckAdmin\User\Model\UserModel@getUserByUsername username=
CALL DuckAdmin\User\Model\UserModel@batchGetUsernames user_ids=
CALL DuckAdmin\User\Model\UserModel@verifyPassword user=&password=
CALL DuckAdmin\User\Model\UserModel@unloadPassword user=
CALL DuckAdmin\User\Model\UserModel@updatePassword uid=&password=
*/
//@codeCoverageIgnoreEnd
