<?php declare(strict_types=1);
namespace DuckAdmin\User\System;

use DuckPhp\Foundation\SingletonTrait;
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
        $list_referenct = <<<EOT
WEB users/Home/index
WEB users/
WEB users/logout
UserBusiness UserBusiness@canAccess user_id=&class=&method=&url=
CALL DuckAdmin\DemoUsers\Business\UserBusiness@log user_id=&string=&type=&ext=
CALL DuckAdmin\DemoUsers\Business\UserBusiness@batchGetUsernames ids=
CALL DuckAdmin\DemoUsers\Business\UserBusiness@login post=

EOT;

        $list = <<<EOT
#PHASE_BEGIN
CALL {static}@beginTest
WEB install driver=sqlite&database%5Bfile%5D={db}&database%5Bhost%5D=127.0.0.1&database%5Bport%5D=&database%5Bdbname%5D=&database%5Busername%5D=&database%5Bpassword%5D=&action=install
WEB index
WEB register
WEB register _token=Nos6FHBP3NNTC39H4JAXuOUY6fGFepgPYgiO7S7l&name=t1&password=123456&password_confirm=123456
WEB register _token=Nos6FHBP3NNTC39H4JAXuOUY6fGFepgPYgiO7S7l&name=t1&password=123456&password_confirm=123456
WEB login _token=Nos6FHBP3NNTC39H4JAXuOUY6fGFepgPYgiO7S7l&name=t1&password=123456&password_confirm=123456

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