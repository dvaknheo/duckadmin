<?php declare(strict_types=1);
/**
 * DuckPhp
 * From this time, you never be alone~
 */
namespace DuckAdminDemo\System;

use DuckPhp\Foundation\SingletonTrait;

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
COMMENT DuckAdminTests;
COMMENT #INCLUDE_CHILD DuckAdmin\Admin\System\AdminApp
COMMENT #INCLUDE_CHILD DuckAdmin\DemoUsers\System\DemoUsersApp
COMMENT #INCLUDE_CHILD DuckAdmin\SingleAdmin\System\SingleAdminApp
#INCLUDE_CHILD DuckAdmin\User\System\UserApp
#PHASE_END

EOT;
        return $list;
    }
   

}