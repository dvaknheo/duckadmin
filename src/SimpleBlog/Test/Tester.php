<?php
namespace SimpleBlog\Test;

use DuckPhp\Foundation\SimpleSingletonTrait;

class Tester
{
    use SimpleSingletonTrait;
    public function getTestList()
    {
        //#COMMAND FUNCTION_METHOD
        $list = <<<EOT

EOT;
        $prefix = \SimpleBlog\System\SimpleBlogApp::_()->options['controller_url_prefix'];
        $list = str_replace('#WEB ',$prefix,$list);
        return $list;
    }
}