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
        $static = static::class;
        $str = <<<'EOT'
#PHASE_BEGIN

#PHASE_END

EOT;
        $user_app = UserApp::class;
        $last_phase = App::Phase();
        
        if (App::_()->toChildPhase($user_app)) {
            $str .= \DuckAdmin\User\System\FullTestLister::_()->getTestList();
            App::Phase($last_phase);
        }
        
        return $str;
    }
   

}