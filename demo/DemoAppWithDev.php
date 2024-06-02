<?php declare(strict_types=1);
/**
 * DuckPhp
 * From this time, you never be alone~
 */
namespace Demo;

use Demo\Tester\MyCoverageBridge;
//use Demo\Tester\TestFileCreator;
use Demo\Test\MyTester;

use DuckPhp\Component\ExtOptionsLoader;
use DuckPhp\Component\DbManager;
use DuckPhp\Core\Route;
use DuckPhp\Core\Console;
use DuckPhp\DuckPhp;
use DuckPhp\FastInstaller\FastInstaller;



require_once(__DIR__. '/Tester/MyCoverage.php');
require_once(__DIR__. '/Tester/MyCoverageBridge.php');
require_once(__DIR__. '/Test/MyTester.php');

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
            
            //'test_callback_class'=> MyTester::class,
            
            'test_list_callback'=>[MyTester::class,'GetTestList'],
            'test_before_after_web'=>[MyTester::class,'BeforeWebTest'],
            'test_before_after_web'=>[MyTester::class,'AfterWebTest'],
            'test_before_replay'=>[MyTester::class,'BeforeReplayTest'],
            'test_after_replay'=>[MyTester::class,'AfterReplayTest'],
            'test_on_report'=>[MyTester::class,'OnReport'],
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
        parent::onPrepare();
        MyCoverageBridge::_()->init($this->options['ext'][MyCoverageBridge::class]);
        MyCoverageBridge::_()->onAppPrepare();
    }
    
}
