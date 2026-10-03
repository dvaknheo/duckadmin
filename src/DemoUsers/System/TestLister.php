<?php declare(strict_types=1);
/**
 * DuckAdmin DemoUsers - 预设用户系统
 * 不使用数据库、不使用安装系统;登录限定 demo_users 数组中的用户
 */
namespace DuckAdmin\DemoUsers\System;

use DuckPhp\Foundation\SingletonTrait;
use DuckPhp\Core\App;

//@codeCoverageIgnoreStart
class TestLister
{
    use SingletonTrait;
    public static function GetTestOrderList(): string
    {
        return static::_()->_GetTestOrderList();
    }
    public static function _GetTestOrderList(): string
    {
        $flag = App::_()->options['user_provider_enable'] ?? true;
        if(!$flag){
            return '';
        }

        $list_referenct = <<<EOT
WEB users/Home/index
WEB users/
WEB users/logout
CALL DuckAdmin\DemoUsers\Business\UserBusiness@canAccess user_id=&url=&class=&method=
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
SETWEB _ _ {phase}!{static}@moreTest2 _
WEB Home/index
WEB logout

#PHASE_END

EOT;
        $list = str_replace('{static}', static::class, $list);
        $list = str_replace('{phase}', App::Phase(), $list);
        return $list;
    }

    public function moreTest2()
    {
        // 三步各自 try:register() 是故意永远抛的("注册已被关闭"),
        // 原来挤在同一个 try 里,它一抛后面的 log()/batchGetUsernames() 就永远轮不到——
        // DemoUsers 的 log 因此一直是 0% 覆盖(本应用唯一没吃到的地方)。
        try {
            \DuckAdmin\DemoUsers\Business\UserBusiness::_()->register([]);
        } catch (\Throwable $ex) {
            // 预期:注册已关闭
        }
        try {
            \DuckAdmin\DemoUsers\Business\UserBusiness::_()->log(0, '');
        } catch (\Throwable $ex) {
            // 忽略
        }
        try {
            // 传 [1,2] 才会走到"命中预设用户"的那条分支(batchGetUsernames 唯一没覆盖的一行)
            \DuckAdmin\DemoUsers\Business\UserBusiness::_()->batchGetUsernames([1,2]);
        } catch (\Throwable $ex) {
            // 忽略
        }
        return;

    }
}//@codeCoverageIgnoreEnd
