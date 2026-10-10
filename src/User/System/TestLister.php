<?php declare(strict_types=1);
namespace DuckAdmin\User\System;


use DuckAdmin\System\TestListHelper;
use DuckCoverage\TestListWithAuthBase;
use DuckPhp\Core\App;

//@codeCoverageIgnoreStart
class TestLister extends TestListWithAuthBase
{
    const TEST_DB_NAME = 'user';
    public function cleanInstall()
    {
        return TestListHelper::_()->cleanInstall();
    }
    public function loadDb()
    {
        return TestListHelper::_()->loadDb();
    }

    public function _GetTestListForClean(): string
    {
        $phase = App::Phase();
        $static = static::class;
        $list = <<<EOT
PHASE {$phase}
{$static}@cleanInstall

EOT;
        return $list;
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
        $dbfile = TestListHelper::_()->getTestDbFile('user');
        $list = <<<EOT
PHASE {$phase}
CALL {$static}@cleanInstall
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
WEB login
WEB Home/index
WEB logout

CALL {$static}@loadDb
#BUSINESS UserAdminBusiness@getUserList

EOT;
        return $list;
    }
    public function moreTest()
    {
        $calls = [
            // 本应用 Controller\Helper 的两个 csrf 包装(控制器目前直接调 Session::_(),
            // 所以它们没被走到)。注意只能用实例方式调:应用自己的 Helper 是普通子类,
            // 它自己新增的方法不是 static,写成 Helper::csrfToken() 会
            
            ['\DuckAdmin\User\Controller\Helper', 'csrfToken', []],
            ['\DuckAdmin\User\Controller\Helper', 'csrfField', []],
            ['\DuckAdmin\User\Controller\Helper', 'checkCsrf', []],
            
            ['\DuckAdmin\User\Business\UserBusiness', 'batchGetUsernames', [[1, 2]]],
            ['\DuckAdmin\User\Business\UserBusiness', 'changePassword', [1, '123456', '123456']],
            ['\DuckAdmin\User\Business\UserBusiness', 'log', [1, 'coverage', 'test']],
            
            // UserAdminBusiness / UserAdminModel：整文件原来 0%
            ['\DuckAdmin\User\Business\UserAdminBusiness', 'getUserList', [[], 1, 10]],
            ['\DuckAdmin\User\Business\UserAdminBusiness', 'getUserList', [['all' => 1], 1, 10]],
            ['\DuckAdmin\User\Business\UserAdminBusiness', 'deleteUser', [1, 2]],
            ['\DuckAdmin\User\Business\UserAdminBusiness', 'unDeleteUser', [1, 2]],

            ['\DuckAdmin\User\Model\UserModel', 'getUserById', [1]],
            ['\DuckAdmin\User\Model\UserModel', 'getUserByUsername', ['t1']],
            ['\DuckAdmin\User\Model\UserModel', 'exsits', ['nobody_xyz']],
            ['\DuckAdmin\User\Model\UserModel', 'addUser', ['t9', '123456']],
            ['\DuckAdmin\User\Model\UserModel', 'updatePassword', [1, '123456']],
            ['\DuckAdmin\User\Model\UserModel', 'batchGetUsernames', [[1, 2]]],
            
            ['\DuckAdmin\User\Model\UserAdminModel', 'getUserList', [[], 1, 5]],
            ['\DuckAdmin\User\Model\UserAdminModel', 'getUserList', [['all' => 1], 1, 5]],
            ['\DuckAdmin\User\Model\UserAdminModel', 'deleteUser', [2]],
            ['\DuckAdmin\User\Model\UserAdminModel', 'unDeleteUser', [2]],
        ];
        foreach ($calls as [$class, $method, $args]) {
            $class::_()->$method(...$args);
        }
    }

}
//@codeCoverageIgnoreEnd
