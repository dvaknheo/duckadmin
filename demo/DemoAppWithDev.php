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
        $this->options['ext'][MyCoverageBridge::class]=[
            'path_src'=> $path_src,
        ];
        $this->options['cli_command_classes'][] = MyCoverageBridge::class;
    }
    public function action_index()
    {
        var_dump("dev");
        Helper::Show([],'main');
    }
    public function command_gentest($uri = '', $post = false)
    {
        $last_phase = static::Phase(\DuckAdmin\System\DuckAdminApp::class);
        $routes = TestFileCreator::_()->genRunFile();
        static::Phase($last_phase);
        var_dump(DATE(DATE_ATOM));
    }
}
