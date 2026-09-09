<?php declare(strict_types=1);
/**
 * DuckAdmin DemoUsers - 预设用户系统
 * 不使用数据库、不使用安装系统;登录限定 demo_users 数组中的用户
 */
namespace DuckAdmin\SingleAdmin\System;

use DuckPhp\Core\App;
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

CALL DuckAdmin\DemoUsers\Business\UserBusiness@canAccess user_id=&class=&method=&url=
CALL DuckAdmin\DemoUsers\Business\UserBusiness@log user_id=&string=&type=&ext=
CALL DuckAdmin\DemoUsers\Business\UserBusiness@batchGetUsernames ids=
CALL DuckAdmin\DemoUsers\Business\UserBusiness@login post=


EOT;

        $list = <<<EOT
#PHASE_BEGIN
WEB index
WEB index password=bad1
WEB index password=123456
WEB index
WEB Home/index
SETWEB _ {static}@checkUser _ _
WEB index
WEB logout

#BUSINESS AdminBusiness@log admin_id=1&string=abc
#BUSINESS AdminBusiness@isSuper admin_id=1

#PHASE_END

EOT;

        $list = str_replace('{static}', static::class, $list);
        $list = str_replace('{phase}', App::Phase(), $list);
        $list = str_replace('#BUSINESS ', 'CALL '. App::Phase().'!'.App::_()->options['namespace']."\\Business\\",$list);

        return $list;
    }
    public function checkUser()
    {
        file_put_contents(__FILE__ .'.log',DATE(DATE_ATOM));
        //Helper::User()->data();
        return;
    }

}