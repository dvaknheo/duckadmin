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
        $flag = DemoApp::_()->options['customer_duckcoverage_only_this_child'] ?? null;
        $list = [];
        $list[] ="#PHASE_BEGIN";
        foreach (DemoApp::_()->options['app'] as $app => $options) {
            if (isset($flag)) {
                if ($app === $flag) {
                    $list[] ="#INCLUDE_CHILD $app";
                }
            } else {
                $list[] ="#INCLUDE_CHILD $app";
            }
        }
        $list[] ="#PHASE_END";
        $ret = implode("\n",$list);
        return $ret;
    }
   

}