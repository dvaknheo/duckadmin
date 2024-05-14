<?php declare(strict_types=1);
/**
 * DuckPhp
 * From this time, you never be alone~
 */
namespace Demo;

use DuckPhp\Core\Route;
use DuckPhp\DuckPhp;

use DuckPhp\Foundation\Helper;

class DemoAppWithDev extends DemoApp
{
    public function __construct()
    {
        parent::__construct();
        $path_src = realpath(__DIR__.'/../src/').'/';
        $this->options['ext'][MyCoverageBridge::class]=[
            'path_src'=> $path_src,
        ];
        $this->options['cli_command_classes'][]=MyCoverageBridge::class;
    }
    public function onInit()
    {
        parent::onInit();
    }
    public function action_index()
    {
        var_dump("DevMode!");
        Helper::Show([],'main');
    }
    public function command_foo($uri = '', $post = false)
    {
        require_once('RouteList.php');
        
        $last_phase = static::Phase(\DuckAdmin\System\DuckAdminApp::class);
        $routes = RouteList::_()->listAllRoutes();
        
        foreach($routes as $method =>$route){
            $route = strtolower($route);
            echo $route;
            echo "\n";
            //echo $route."\t\t\t\t".$method ."\n";
        }
        static::Phase($last_phase);
    }
}
