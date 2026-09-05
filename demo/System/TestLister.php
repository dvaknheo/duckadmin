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
        $user_app = UserApp::class;
        $str = <<<EOT
#PHASE_BEGIN
#INCLUDE_CHILD {$user_app}
#PHASE_END

EOT;
        return $str;
    }
   

}