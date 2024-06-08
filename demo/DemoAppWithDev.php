<?php declare(strict_types=1);
/**
 * DuckPhp
 * From this time, you never be alone~
 */
namespace DuckAdminDemo;

use DuckAdminDemo\Tester\MyCoverageBridge;
use DuckAdminDemo\Test\MyTester;


class DemoAppWithDev extends DemoApp
{
    public function __construct()
    {
        parent::__construct();
        $path_src = realpath(__DIR__.'/../src/').'/';
        
        // 设置 测试类
        $tester_options = [
            'path_src'=> $path_src,
            'test_server_port'=> 8080,
            'test_homepage' =>'/index_dev.php/',
            'test_path_document'=>'public',
            'test_new_server'=>true,
            
            'test_callback_class'=> MyTester::class,
        ];
        
        $this->options['ext'][MyCoverageBridge::class] = $tester_options;
        
        $this->options['database_driver']='sqlite';
    }
    public function action_index()
    {
        var_dump('devmode');1
        //Helper::AssignViewData();
        parent::action_index();
        
    }
    public function onPrepare()
    {
        parent::onPrepare();
        MyCoverageBridge::_()->init($this->options['ext'][MyCoverageBridge::class])->onAppPrepare();
    }
    
}
