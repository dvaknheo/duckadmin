<?php declare(strict_types=1);
/**
 * DuckPhp
 * From this time, you never be alone~
 */
namespace Demo;

use Demo\Tester\MyCoverageBridge;
use Demo\Tester\TestFileCreator;
use DuckPhp\Component\ExtOptionsLoader;
use DuckPhp\Component\DbManager;
use DuckPhp\Core\Route;
use DuckPhp\Core\Console;
use DuckPhp\DuckPhp;
use DuckPhp\Foundation\Helper;
use DuckPhp\FastInstaller\FastInstaller;
//use DuckPhp\Foundation\System\Helper;



require_once(__DIR__. '/Tester/MyCoverage.php');
require_once(__DIR__. '/Tester/MyCoverageBridge.php');

class DemoAppWithDev extends DemoApp
{
    public function __construct()
    {
        parent::__construct();
        $path_src = realpath(__DIR__.'/../src/').'/';
        
        $tester_options = [
            'path_src'=> $path_src,
            'test_server_port'=> 8080,
            'test_homepage' =>'/index_dev.php/',
            'test_path_document'=>'public',
            'test_new_server'=>true,
            'test_list_callback'=>[static::class,'GetTestList'],
            'test_before_after_web'=>[static::class,'BeforeWebTest'],
            'test_before_after_web'=>[static::class,'AfterWebTest'],
            'test_before_replay'=>[static::class,'BeforeReplayTest'],
            'test_after_replay'=>[static::class,'AfterReplayTest'],
        ];
        $this->options['ext_options_file']='config/DuckPhpApps_dev.config.php';
        $this->options['ext'][MyCoverageBridge::class] = $tester_options;
        
        
        $this->options['database_driver']='sqlite';
    }
    public function action_index()
    {
        var_dump("dev");
        parent::action_index();
        
    }
    public function onPrepare()
    {
        //Helper::SERVER('HTTP_X_MYCOVERAGE_NAME','');
        $flag1 = $_SERVER['argv'][1] ?? '';
        $flag2 = $_SERVER['HTTP_X_MYCOVERAGE_NAME'] ?? '';
        if($flag1 ==='testgroup' || $flag2){
            $this->options['ext_options_file'] = 'runtime/DuckPhpApps_test.config.php';
        }
        if($flag1 ==='testgroup'){
            $this->options['ext_options_file_enable'] =false;
        }
        return parent::onPrepare();
    }
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
        return static::_()->_BeforeReplayTest();
    }
    public static function AfterReplayTest()
    {
        return static::_()->_AfterReplayTest();
    }

    public function installTest()
    {
        @unlink($this->options['ext_options_file']);
        $this->options['ext_options_file_enable']=true;
        $db_file = 'db_fortest.db';
        @unlink(Helper::PathOfRuntime().$db_file);
        $input = <<<EOT
{$db_file}
n
admin
123456
123456

EOT;
        Console::_()->readLinesFill($input);
        FastInstaller::_()->doInstall(); // App::_()->callConsole('install');
        var_dump(DATE(DATE_ATOM));
    }
    public function cleanAll()
    {
        @unlink($this->options['ext_options_file']);
        $this->options['ext_options_file_enable']=true;
        $db_file = 'db_fortest.db';
        @unlink(Helper::PathOfRuntime().$db_file);
    }
    public function _GetTestList()
    {
        
        $str ='';
        //$str .= \DuckAdmin\Test\Tester::_()->getTestList();
        $str .= \DuckUser\Test\Tester::_()->getTestList();
        //$str .= \SimpleBlog\Test\Tester::_()->getTestList();
        //$str .= \DuckUserManager\Test\Tester::_()->getTestList();
        
        //path = \DuckAdmin\DuckAdminApp::_()->options['path'];
        //$filter = MyCoverageBridge::_()->getCoverage()->filter();
        //$filter->removeDirectoryFromWhitelist($path.'Test');
        //$filter->removeDirectoryFromWhitelist($path.'View');
        //    public function removeDirectoryFromWhitelist(string $directory, string $suffix = '.php', string $prefix = ''): void
        //    public function removeFileFromWhitelist(string $filename): void

        return $str;
    }
    public function _BeforeWebTest()
    {
    }
    public function _AfterWebTest()
    {
        //
    }
    public function _BeforeReplayTest()
    {
        $this->installTest();
    }
    public function _AfterReplayTest()
    {
        $this->cleanAll();
    }
}
