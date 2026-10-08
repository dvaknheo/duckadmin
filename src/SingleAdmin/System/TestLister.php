<?php

declare(strict_types=1);

namespace DuckAdmin\SingleAdmin\System;

use DuckAdmin\System\TestListWithAuthBase;
use DuckPhp\Core\App;

//@codeCoverageIgnoreStart
class TestLister extends TestListWithAuthBase
{
    public function _GetTestListForLogin(): string
    {
        $phase = App::Phase();
        $password = (string)(App::_()->options['single_admin_password'] ?? '123456');
        $list = <<<EOT
PHASE {$phase}
WEB index password={$password}

EOT;
        return $list;
    }
    public function _GetTestListForLogout(): string
    {
        $phase = App::Phase();
        $list = <<<EOT
PHASE {$phase}
WEB logout

EOT;
        return $list;
    }
    public function _GetTestListFull(): string
    {
        $phase = App::Phase();
        $list = <<<EOT
PHASE {$phase}
WEB index
WEB index password=bad1
WEB index password=123456
WEB index
WEB Home/index
WEB index
WEB logout

#BUSINESS AdminBusiness@log admin_id=1&string=abc
#BUSINESS AdminBusiness@isSuper admin_id=1

EOT;
        $list = str_replace('#BUSINESS ', 'CALL ' . App::Phase() . '!' . App::_()->options['namespace'] . "\\Business\\", $list);
        return $list;
    }
}//@codeCoverageIgnoreEnd
