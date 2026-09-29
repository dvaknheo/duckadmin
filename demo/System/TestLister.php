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
        $list = [];
        $list[] ="#PHASE_BEGIN";
        foreach (DemoApp::_()->options['app'] as $app => $options) {
            $list[] ="#INCLUDE_CHILD $app";
        }
        $list[] ="#PHASE_END";
        return implode("\n",$list);
    }
   

}