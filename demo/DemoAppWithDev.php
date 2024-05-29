<?php declare(strict_types=1);
/**
 * DuckPhp
 * From this time, you never be alone~
 */
namespace Demo;

use DuckPhp\Core\Route;
use DuckPhp\DuckPhp;
use DuckPhp\Foundation\Helper;
//use DuckPhp\Foundation\System\Helper;

use Demo\Tester\MyCoverageBridge;
use Demo\Tester\TestFileCreator;

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
            'test_before_after_web'=>[static::class,'BeforeWeb'],
            'test_before_after_web'=>[static::class,'AfterWeb'],
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
    public static function BeforeWeb()
    {
        return static::_()->_BeforeWeb();
    }
    public static function AfterWeb()
    {
        return static::_()->_AfterWeb();
    }
    public static function GetTestList()
    {
        return static::_()->_GetTestList();
    }
    public static function _GetTestList()
    {
        $str = '';
        $str .= \DuckAdmin\Test\Tester::_()->getTestList();
        //$str .= \DuckUser\Test\Tester::_()->getTestList();
        //$str .= \DuckUserManager\Test\Tester::_()->getTestList();
        //$str .= \SimpleBlog\Test\Tester::_()->getTestList();
                
        //path = \DuckAdmin\DuckAdminApp::_()->options['path'];
        //$filter = MyCoverageBridge::_()->getCoverage()->filter();
        //$filter->removeDirectoryFromWhitelist($path.'Test');
        //$filter->removeDirectoryFromWhitelist($path.'View');
        //    public function removeDirectoryFromWhitelist(string $directory, string $suffix = '.php', string $prefix = ''): void
        //    public function removeFileFromWhitelist(string $filename): void

        return $str;
    }
    public function _BeforeWeb()
    {
        //
    }
    public function _AfterWeb()
    {
        //
    }
}
