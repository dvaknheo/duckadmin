<?php declare(strict_types=1);
/**
 * DuckPhp
 * From this time, you never be alone~
 */
namespace DuckAdminDemo\Test;

use DuckPhp\Core\App;
use DuckPhp\Core\Console;
use DuckPhp\Foundation\SingletonTrait;
use DuckPhp\Foundation\Helper;

class MyTester
{
    use SingletonTrait;

    public static function BeforeWebTest()
    {
        return static::_()->_BeforeWebTest();
    }
    public static function AfterWebTest()
    {
        return static::_()->_AfterWebTest();
    }
    public static function GetTestList()
    {
        return static::_()->_GetTestList();
    }
    public static function BeforeReplayTest()
    {
        return static::_()->installTest();
    }
    public static function AfterReplayTest()
    {
        //return static::_()->_AfterReplayTest();
    }
    public static function OnReport()
    {
        //return static::_()->_OnReport();
    }
    public function installTest()
    {
        var_dump(DATE(DATE_ATOM));
    }
    public function cleanAll()
    {
        @unlink(App::_()->options['ext_options_file']);
        App::_()->options['ext_options_file_enable']=true;
        $db_file = 'db_fortest.db';
        @unlink(Helper::PathOfRuntime().$db_file);
    }

    public function _GetTestList()
    {
        $static = static::class;
        $str ='';
        //$str.="#CALL {$static}@installTest\n";
        $str .= \DuckAdmin\Test\Tester::_()->getTestList();
        $str .= \DuckUser\Test\Tester::_()->getTestList();
        $str .= \SimpleBlog\Test\Tester::_()->getTestList();
        $str .= \DuckUserManager\Test\Tester::_()->getTestList();
        $str.="#CALL {$static}@cleanAll\n";
        return $str;
    }
    public function _BeforeWebTest()
    {
    }
    public function _AfterWebTest()
    {
        //
    }

    
    public function _OnReport()
    {
        return;
    }
}