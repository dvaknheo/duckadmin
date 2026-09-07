<?php declare(strict_types=1);
/**
 * DuckPhp
 * From this time, you never be alone~
 */
namespace DuckAdminDemo\System;

use DuckPhp\Core\App;
use DuckPhp\Foundation\SingletonTrait;
use DuckPhp\Foundation\Helper;
use DuckAdmin\User\System\UserApp;

class TestLister
{
    use SingletonTrait;

    public static function GetTestList()
    {
        return static::_()->_GetTestList();
    }
    public function _GetTestList()
    {
        $list = <<<EOT
#PHASE_BEGIN
COMMENT XXX
#INCLUDE_CHILD DuckAdmin\Admin\System\AdminApp
#INCLUDE_CHILD DuckAdmin\DemoUsers\System\DemoUsersApp
#INCLUDE_CHILD DuckAdmin\SingleAdmin\System\SingleAdminApp
#INCLUDE_CHILD DuckAdmin\User\System\UserApp
#PHASE_END

EOT;
        return $list;
    }
   

}