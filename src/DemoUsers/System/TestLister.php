<?php declare(strict_types=1);
/**
 * DuckAdmin DemoUsers - 预设用户系统
 * 不使用数据库、不使用安装系统;登录限定 demo_users 数组中的用户
 */
namespace DuckAdmin\DemoUsers\System;

use DuckPhp\Foundation\SingletonTrait;
use DuckPhp\Core\App;
use DuckPhp\Foundation\Controller\Helper;

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
        try {

            \DuckAdmin\DemoUsers\Business\UserBusiness::_()->register([]);
            \DuckAdmin\DemoUsers\Business\UserBusiness::_()->log(0, '');
            \DuckAdmin\DemoUsers\Business\UserBusiness::_()->batchGetUsernames([1,2]);
            
        } catch (\Throwable $ex) {
            file_put_contents(__FILE__.'.error.log',$ex->getMessage().PHP_EOL.$ex->getTraceAsString().PHP_EOL,FILE_APPEND);
        }
        return;

    }
}//@codeCoverageIgnoreEnd
