<?php declare(strict_types=1);
/**
 * DuckAdmin DemoUsers - 预设用户系统
 * 不使用数据库、不使用安装系统;登录限定 demo_users 数组中的用户
 */
namespace DuckAdmin\DemoUsers\System;

use DuckPhp\Foundation\SingletonTrait;


class TestLister
{
    use SingletonTrait;
    public static function GetTestOrderList(): string
    {
        return static::_()->_GetTestOrderList();
    }
    public static function GetShortTestOrderList(): string
    {
        return static::_()->_GetShortTestOrderList();
    }

    public static function _GetTestOrderList(): string
    {
        $list = '';
        return $list;
    }

}
/*
// 首先，我们要搞安装系统
我们从注册登录

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