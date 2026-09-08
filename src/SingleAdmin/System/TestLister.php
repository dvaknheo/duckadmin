<?php declare(strict_types=1);
/**
 * DuckAdmin DemoUsers - 预设用户系统
 * 不使用数据库、不使用安装系统;登录限定 demo_users 数组中的用户
 */
namespace DuckAdmin\SingleAdmin\System;

use DuckPhp\Foundation\SingletonTrait;
use DuckPhp\Foundation\Controller\Helper;


class TestLister
{
    use SingletonTrait;
    public static function GetTestOrderList(): string
    {
        return static::_()->_GetTestOrderList();
    }
    public static function _GetTestOrderList(): string
    {
        $list_referenct = <<<EOT
WEB users/Home/index
WEB users/
WEB users/logout
CALL DuckAdmin\DemoUsers\Business\UserBusiness@canAccess user_id=&class=&method=&url=
CALL DuckAdmin\DemoUsers\Business\UserBusiness@log user_id=&string=&type=&ext=
CALL DuckAdmin\DemoUsers\Business\UserBusiness@batchGetUsernames ids=
CALL DuckAdmin\DemoUsers\Business\UserBusiness@login post=


EOT;

        $list = <<<EOT
#PHASE_BEGIN
WEB index
WEB index username=t1&password=bad1
WEB index username=t1&password=123456
WEB index
WEB Home/index
SETWEB _ {static}@checkUser _ _
WEB index
WEB logout
#PHASE_END

EOT;
        $list = str_replace('{static}', static::class, $list);
        return $list;
    }
    public function checkUser()
    {
        file_put_contents(__FILE__ .'.log',DATE(DATE_ATOM));
        Helper::User()->data();
        return;
    }

}