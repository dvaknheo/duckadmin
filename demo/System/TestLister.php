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
        $user_app = UserApp::class;
        $last_phase = App::Phase();
        if (App::_()->toChildPhase($user_app)) {
            $str .= \DuckAdmin\User\System\FullTestLister::_()->getTestList();
            App::Phase($last_phase);
        }
        
        return $str;
    }

}