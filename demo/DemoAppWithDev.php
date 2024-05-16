<?php declare(strict_types=1);
/**
 * DuckPhp
 * From this time, you never be alone~
 */
namespace Demo;

use DuckPhp\Core\Route;
use DuckPhp\DuckPhp;
use DuckPhp\Foundation\Helper;
use Demo\Test\MyCoverageBridge;
use Demo\Test\TestFileCreator;

require_once(__DIR__. '/Test/MyCoverage.php');
require_once(__DIR__. '/Test/MyCoverageBridge.php');
require_once(__DIR__. '/Test/TestFileCreator.php');

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
        ];
        
        $this->options['ext'][MyCoverageBridge::class] = $tester_options;
    }
    public function action_index()
    {
        var_dump("dev");
        parent::action_index();
    }
    public static function GetTestList()
    {
        return static::_()->_GetTestList();
    }
    public static function _GetTestList()
    {
        $routes_text= \DuckAdmin\Test\RunAll::_()->getAllRouteToRun();
        $prefix=\DuckAdmin\System\DuckAdminApp::_()->options['controller_url_prefix'];
        $routes_text = str_replace('#WEB ',$prefix,$routes_text);
        return $routes_text;
    }
}
