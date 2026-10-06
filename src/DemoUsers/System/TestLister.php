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
        // 被别的应用 embed 时,按调用方塞的一次性参数决定给哪种清单
        // 本应用（管理员/用户系统之一）的清单类别：被别的应用嵌入时，按调用方塞的一次性参数决定。
        // 参数放在**根应用**上，读到就清，避免影响同一次 play 里后续的清单生成。
        $root = \DuckPhp\Core\App::Root();
        $cmd = (string)($root->options['duckcoverage_test_lister_parameter'] ?? '');
        if ($cmd !== '') {
            unset($root->options['duckcoverage_test_lister_parameter']);
        }
        switch ($cmd) {
            case 'login':
                return static::_()->_GetTestListForLogin();
            case 'logout':
                return static::_()->_GetTestListForLogout();
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
    public static function GetTestListForLogin(): string
    {
        return static::_()->_GetTestListForLogin();
    }
    public function _GetTestListForLogin(): string
    {
        // 「只建立登录态」：DemoUsers 是预设账号,登录一行就够;
        // 不安装、不登出 —— 于是可以被别的应用反复 embed。
        // 相对 uri —— 当前 phase 就是 DemoUsersApp,__url() 自动补成 /demo_users/。
        $list = <<<EOT
#PHASE_BEGIN
WEB index
WEB index username=t1&password=123456
#PHASE_END
EOT;
        return $list;
    }
    public static function GetTestListForLogout(): string
    {
        return static::_()->_GetTestListForLogout();
    }
    public function _GetTestListForLogout(): string
    {
        $list = <<<EOT
#PHASE_BEGIN
WEB logout
#PHASE_END
EOT;
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
