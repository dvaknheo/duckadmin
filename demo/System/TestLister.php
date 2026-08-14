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

class TestLister
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
        // 通过 App 切到 User 子 phase 调用子 Tester;
        // #PHASE / #URL_PREFIX 头部指令由子 Tester 的 getTestList() 自身生成
        $user_app = \DuckAdmin\User\System\DuckUserApp::class;
        $last_phase = App::Phase();
        if (App::_()->toChildPhase($user_app)) {
            $str .= \DuckAdmin\User\Test\Tester::_()->getTestList();
            App::Phase($last_phase);
        }
        
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