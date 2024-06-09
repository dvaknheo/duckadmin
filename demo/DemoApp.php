<?php declare(strict_types=1);
/**
 * DuckPhp
 * From this time, you never be alone~
 */
namespace DuckAdminDemo;

use DuckPhp\DuckPhp;
use DuckPhp\Foundation\Controller\Helper;
use DuckAdminDemo\Tester\MyCoverageBridge;
use DuckAdminDemo\Test\MyTester;

class DemoApp extends DuckPhp
{
    public $options = [
        'is_debug' => true,
        'cli_command_with_fast_installer' => true,  //for install command
        
        'path' => __DIR__.'/',
        'namespace' => 'DuckAdminDemo',
        
        'controller_resource_prefix' => '/',  //for workerman local file
        'path_resource' => 'public',          //for workerman local file
        
        'database_driver' => 'sqlite',
        'duckadmin_demo_enable_test' => true,
        'duckadmin_demo_enable_workerman' => true,
        
        'app' => [
//*
            \DuckAdmin\System\DuckAdminApp::class => [      // 后台管理系统
                'controller_url_prefix' => 'app/admin/',    // 访问路径
                'controller_resource_prefix' => '/res/app/admin/',     // 资源文件前缀
            ],
//*/
            \DuckUser\System\DuckUserApp::class => [
                'controller_url_prefix' => 'user/',             // 访问路径
                'controller_resource_prefix' => '/res/user',    // 资源文件前缀
            ],
//*/
            \DuckUserManager\System\DuckUserManagerApp::class => [
                'controller_url_prefix' => 'app/admin/',            // 访问路径
                'controller_resource_prefix' => '/res/app/admin/',  // 资源文件前缀
            ],
//*/
            \SimpleBlog\System\SimpleBlogApp::class => [
                'controller_url_prefix' => 'blog/',                 // 访问路径
                'controller_resource_prefix' => '/res/blog',        // 资源文件前缀
            ],
//*/
        ],
    ];
    public function __construct()
    {
        // embed welcomepage to this class
        $path = explode('\\', static::class);
        $short_class = array_pop($path);
        $ext_options =  [
            'namespace_controller' =>  "\\" . __NAMESPACE__ ,
            'controller_welcome_class' => $short_class ,
            'controller_class_postfix' => '',
        ];
        
        $this->options = array_merge($this->options,$ext_options); 
        parent::__construct();
    }
    public function action_index()
    {
        // index page
        $data = [];
        $data['url_blog'] = __url(\SimpleBlog\System\SimpleBlogApp::_()->options['controller_url_prefix']) . 'index';
        $data['url_user'] = __url(\DuckUser\System\DuckUserApp::_()->options['controller_url_prefix']) . 'index';
        $data['url_admin'] = __url(\DuckAdmin\System\DuckAdminApp::_()->options['controller_url_prefix']) . 'index';
        $data['url_user_manager'] = __url(\DuckUserManager\System\DuckUserManagerApp::_()->options['controller_url_prefix']) . 'index';
        
        $data ['duckadmin_demo_enable_test'] = $this->options['duckadmin_demo_enable_test'];
        
        Helper::Show($data,'main');
    }
    public function onPrepare()
    {
        parent::onPrepare();
        
        if ($this->options['duckadmin_demo_enable_workerman']) {
            \DuckPhp\HttpServer\HttpServer::_(\WorkermanHttpd\WorkermanHttpd::_());
        }
        if ($this->options['duckadmin_demo_enable_test']) {
            $this->enableTest();
        }
    }
    public function onInited()
    {
        parent::onInited();
        // You Codes Here.
        if ($this->options['duckadmin_demo_enable_test']) {
            $this->enableTest(); //TODO
            //MyCoverageBridge::registCommand();
        }
    }
    
    public function command_hello()
    {
        // show a command demo
        echo "From this time, you never be alone~\n";
    }
    protected function enableTest()
    {
        // for coverage test
        $path_src = realpath(__DIR__.'/../../src/').'/';
        $path_src = realpath(__DIR__.'/../src/').'/';
        $tester_options = [
            'path_src'=> $path_src,
            'test_server_port'=> 8080,
            'test_homepage' =>'/index.php/',
            'test_path_document'=>'public',
            'test_new_server'=>true,
            
            'test_callback_class'=> MyTester::class,
        ];
        MyCoverageBridge::_()->init($tester_options)->onAppPrepare();
    }
}
