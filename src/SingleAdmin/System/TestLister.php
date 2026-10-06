<?php declare(strict_types=1);
/**
 * DuckAdmin DemoUsers - 预设用户系统
 * 不使用数据库、不使用安装系统;登录限定 demo_users 数组中的用户
 */
namespace DuckAdmin\SingleAdmin\System;

use DuckPhp\Core\App;
use DuckPhp\Foundation\SingletonTrait;

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
        $flag = App::_()->options['admin_provider_enable'] ?? true;
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

CALL DuckAdmin\DemoUsers\Business\UserBusiness@canAccess user_id=&url=&class=&method=
CALL DuckAdmin\DemoUsers\Business\UserBusiness@log user_id=&string=abc&type=&ext=
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
    public static function GetTestListForLogin(): string
    {
        return static::_()->_GetTestListForLogin();
    }
    public function _GetTestListForLogin(): string
    {
        // 「只建立登录态」：SingleAdmin 是配置式单管理员、页面恒定已登录,
        // 所以这里走一次登录表单即可(顺带覆盖 IndexController 的登录分支),不需要登出。
        // 相对 uri：当前 phase 就是 SingleAdminApp,自动补成 /single_admin/。
        $password = (string)(App::_()->options['single_admin_password'] ?? '123456');
        $list = <<<EOT
#PHASE_BEGIN
WEB index
WEB index password={$password}
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
        // 单管理员没有"登出"这一步,写明原因而不是返回空清单,便于排查
        return "#PHASE_BEGIN\nCOMMENT SingleAdmin 是配置式单管理员,没有登出这一步\n#PHASE_END";
    }
    public function checkUser()
    {
        //Helper::User()->data();
        return;
    }

}//@codeCoverageIgnoreEnd
