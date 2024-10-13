<?php declare(strict_types=1);
/**
 * DuckPhp
 * From this time, you never be alone~
 */
namespace DuckAdminDemo\System;


use DuckPhp\Component\DbManager;
use DuckPhp\Core\CoreHelper;
use DuckPhp\DuckPhp;
use DuckPhp\Foundation\Controller\Helper;
use DuckCoverage\MyCoverageBridge;
use DuckAdmin\Controller\AccountController;
use DuckAdminDemo\Test\MyTester;

class DemoApp extends DuckPhp
{
    public $options = [
        'is_debug' => true,
        'cli_command_with_fast_installer' => true,  //for install command
        
        'path' => __DIR__.'/../',
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
                'controller_resource_prefix' => 'res/',     // 资源文件前缀
            ],
//*/
            \DuckUser\System\DuckUserApp::class => [
                'controller_url_prefix' => 'user/',             // 访问路径
                'controller_resource_prefix' => 'res/',    // 资源文件前缀
            ],
//*/
            \DuckUserManager\System\DuckUserManagerApp::class => [
                'controller_url_prefix' => 'app/admin/',            // 访问路径
                'controller_resource_prefix' => 'res/',  // 资源文件前缀
            ],
//*/
            \SimpleBlog\System\SimpleBlogApp::class => [
                'controller_url_prefix' => 'blog/',                 // 访问路径
                'controller_resource_prefix' => 'res/',        // 资源文件前缀
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
        $data['url_user_manager'] = __url(\DuckUserManager\System\DuckUserManagerApp::_()->options['controller_url_prefix']) . 'user/index';
        
        $data ['duckadmin_demo_enable_test'] = $this->options['duckadmin_demo_enable_test'];
        
        Helper::Show($data,'main');
    }
    public function onPrepare()
    {
        parent::onPrepare();
        
        //use workerman
        if ($this->options['duckadmin_demo_enable_workerman']) {
            \DuckPhp\HttpServer\HttpServer::_(\WorkermanHttpd\WorkermanHttpd::_())->options['host']='0.0.0.0';
        }
        
        //eanable test
        if ($this->is_root) {
            $this->loadSetting(); //:(
        }
        if (static::Setting('duckadmin_demo_enable_test') || $this->options['duckadmin_demo_enable_test']) {
            $this->enableTest();
        }
    }
    public function onInited()
    {
        $this->checkDemoDb(); // if no default sqlite db file ，create it
        
        parent::onInited();
        // You Codes Here.
        
        CoreHelper::PhaseCall(
            \DuckAdmin\System\DuckAdminApp::class,function(){
                AccountController::_(MyAccountController::_());
        });
        
    }
    protected function checkDemoDb()
    {
        if (static::Setting('duckadmin_demo_enable_test') || $this->options['duckadmin_demo_enable_test']) {
            return;
        }
        $dsn = $this->options['database_list'][0]['dsn']??null;
        if ($dsn !=='sqlite:demodb.db') {
            return;
        }
        $file = realpath(CoreHelper::PathOfRuntime()). '/demodb.db';
        if(is_file($file)){
            return;
        }
        
        $sqlfile = 'demodb.sql';
        $full_file = $this->extendFullFile($this->options['path'], $this->options['path_config']??'config', $sqlfile);
        
        $sql = file_get_contents($full_file);
        $sqls = explode(";\n", ''.$sql);
        foreach ($sqls as $sql) {
            if (empty($sql)) {
                continue;
            }
            $flag = DbManager::Db()->execute($sql);
        }

    }
    /**
     * show a hello world in console.
     */
    public function command_hello()
    {
        // show a command demo
        echo "From this time, you never be alone~\n";
    }
    /**
     * show a hello world in console.
     */
    public function command_t2()
    {
        // show a command demo
        echo "From this time, you never be alone~\n";
        $x = \DuckAdmin\Business\FinderForAdminController::_()->init([])->getAllAdminMethod();
        var_dump($x);

    }
    protected function enableTest()
    {
        // for coverage test
        $path_src = realpath(__DIR__.'/../../src/').'/';
        $tester_options = [
            'path_src'=> $path_src,
            'test_callback_class'=> MyTester::class,
            
            'test_server_port'=> 8080,
            'test_homepage' =>'/index.php/',
            'test_path_document'=>'public',
            'test_new_server'=>true,
        ];
        // sorry this is specail. must before init;
        MyCoverageBridge::_()->init($tester_options)->onAppPrepare();
    }
}
