<?php
namespace DuckUserManager\Test;

use DuckPhp\Foundation\SimpleSingletonTrait;

class Tester
{
    use SimpleSingletonTrait;
    public function getTestList()
    {
        //#COMMAND FUNCTION_METHOD
        $list = <<<EOT

EOT;
        $prefix = \DuckUserManager\System\DuckUserManagerApp::_()->options['controller_url_prefix'];
        $list = str_replace('#WEB ',$prefix,$list);
        return $list;
    }
}