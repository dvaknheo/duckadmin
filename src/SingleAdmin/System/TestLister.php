<?php

declare(strict_types=1);

namespace DuckAdmin\SingleAdmin\System;

use DuckPhp\Core\App;
use DuckPhp\Foundation\SingletonTrait;
use DuckCoverage\DuckCoverage;
use DuckCoverage\TestListerHelper;


//@codeCoverageIgnoreStart
class TestLister
{
    use SingletonTrait;
    public static function GetTestList(): string
    {
        $out = static::_()->_GetTestListFromCategory();
        return $out = \DuckCoverage\TestListerHelper::_()->explainMarco($out);
    }
    public function _GetTestListFromCategory(): string
    {
        $flag = App::_()->options['admin_provider_enable'] ?? true;
        if (!$flag) {
            return '';
        }

        $cmd = App::_()->options['duckcoverage_test_lister_parameter'] ?? '';
        unset(App::_()->options['duckcoverage_test_lister_parameter']);
        switch ($cmd) {
            case 'login':
                return static::_()->_GetTestListForLogin();
            case 'logout':
                return static::_()->_GetTestListForLogout();
            default:
                return static::_()->_GetTestListFull();
        }
    }
    public function _GetTestListFull(): string
    {
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
        $list = str_replace('#BUSINESS ', 'CALL ' . App::Phase() . '!' . App::_()->options['namespace'] . "\\Business\\", $list);

        return $list;
    }
    public static function GetTestListForLogin(): string
    {
        return static::_()->_GetTestListForLogin();
    }
    public function _GetTestListForLogin(): string
    {
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
            $list = <<<EOT
#PHASE_BEGIN
WEB logout
#PHASE_END
EOT;
        return $list;
    }
}//@codeCoverageIgnoreEnd
