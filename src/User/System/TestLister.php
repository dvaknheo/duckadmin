<?php declare(strict_types=1);
namespace DuckAdmin\User\System;

use DuckCoverage\TestListWithAuthBase;
use DuckAdmin\System\DuckAdminApp;
use DuckPhp\Core\App;

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
        $list = <<<EOT
PHASE {$phase}
WEB register name=t1&password=123456&password_confirm=123456
WEB login name=t1&password=123456

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
        $static = static::class;
        $dbfile = DuckAdminApp::_()->getTestDbFile('user');

        $list = <<<EOT
PHASE {$phase}
CALL {$static}@beginTest
COMMENT 先 GET 一次安装页,渲染「表单分支」;再 POST 安装,渲染「完成页分支」。
COMMENT 只做 POST 的话 view/install.php 的表单分支永远覆盖不到(实测 31%)
WEB install
WEB install driver=sqlite&database%5Bfile%5D={$dbfile}&database%5Bhost%5D=127.0.0.1&database%5Bport%5D=&database%5Bdbname%5D=&database%5Busername%5D=&database%5Bpassword%5D=&action=install

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

#ADMIN_LOGIN
PHASE {$phase}
WEB Admin/index
WEB Admin/index?all=1
WEB Admin/delete?id=2&hash=deadbeefdeadbeefdeadbeefdeadbeef
WEB Admin/index?all=1
WEB Admin/index

SETWEB _ _ {$phase}!{$static}@moreTest _
WEB login?lang=en-US
WEB Home/index?lang=en-US
WEB logout

CALL {$static}@endTest

EOT;
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
        // 注意：本钩子是 SETWEB 的 post_webcall，它在**回放进程（CLI）**里执行，
        // 不在被测应用的请求里 —— 所以这里写 $_SESSION 对应用的会话没有任何影响
        // （实测：写过也读不到）。删除/还原要用的合法 hash 因此没法在这里"预制"，
        // 清单里只保留了负例（错 hash → 校验必须拒绝）。见下面 Admin 那几步的注释。
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
            // 本应用 Controller\Helper 的两个 csrf 包装(控制器目前直接调 Session::_(),
            // 所以它们没被走到)。注意只能用实例方式调:应用自己的 Helper 是普通子类,
            // 它自己新增的方法不是 static,写成 Helper::csrfToken() 会
            // "cannot be called statically"。
            ['\DuckAdmin\User\Controller\Helper', 'csrfToken', []],
            ['\DuckAdmin\User\Controller\Helper', 'csrfField', []],
            ['\DuckAdmin\User\Controller\Helper', 'checkCsrf', []],
        ];
        foreach ($calls as [$class, $method, $args]) {
            try {
                $class::_()->$method(...$args);
            } catch (\Throwable $ex) {
                //TODO DuckCoverage->logException($ex);
            }
        }
    }

}
//@codeCoverageIgnoreEnd
