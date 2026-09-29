<?php declare(strict_types=1);
/**
 * DuckPhp
 * From this time, you never be alone~
 */
namespace DuckAdmin\System;

use DuckPhp\Core\App;
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
        $list[] ="#INCLUDE_CHILD ";
        
        $list[] ="#PHASE_END";
        $ret = implode("\n",$list);
        return $ret;
    }
   

}