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
use DuckAdminDemo\Overrided\MyAccountController;
use DuckAdminDemo\Test\MyTester;

class DemoApp extends DuckPhp
{
    public $options = [
        
        'path' => __DIR__.'/../',
        'namespace' => 'DuckAdminDemo',
        
        'controller_resource_prefix' => '/',  //for workerman local file
        'path_resource' => 'public',          //for workerman local file
        
        'duckadmin_demo_enable_test' => false,
        
        'app' => [
//*
            \DuckAdmin\Admin\System\AdminApp::class => [
                'controller_url_prefix' => 'admin/',
                'controller_resource_prefix' => 'res/',
            ],
//*/
            \DuckAdmin\User\System\DuckUserApp::class => [
                'controller_url_prefix' => 'user/',             // 访问路径
                'controller_resource_prefix' => 'res/',    // 资源文件前缀
            ],
//*/
            \SimpleBlog\System\SimpleBlogApp::class => [
                'controller_url_prefix' => 'blog/',
                'controller_resource_prefix' => 'res/',
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
            'controller_method_prefix' => 'action_',
        ];
        
        $this->options = array_merge($this->options,$ext_options); 
        parent::__construct();
    }
    public function action_index()
    {
        $data = [];
        $data['url_blog'] = __url(\SimpleBlog\System\SimpleBlogApp::_()->options['controller_url_prefix']) . 'index';
        $data['url_user'] = __url(\DuckAdmin\User\System\DuckUserApp::_()->options['controller_url_prefix']) . 'index';
        $data['url_admin'] = __url(\DuckAdmin\Admin\System\AdminApp::_()->options['controller_url_prefix']) . 'index';
        
        $data ['duckadmin_demo_enable_test'] = $this->options['duckadmin_demo_enable_test'];
        
        Helper::Show($data,'main');
    }

    public function onInited(): void
    {
        //use workerman
        if ($this->options['duckadmin_demo_enable_workerman']??false) {
            \DuckPhp\HttpServer\HttpServer::_(\WorkermanHttpd\WorkermanHttpd::_())->options['host']='0.0.0.0';
        }
        
        if (static::Setting('duckadmin_demo_enable_test') || $this->options['duckadmin_demo_enable_test']??false) {
            //$this->enableTest();
        }
        //$this->checkDemoDb(); // if no default sqlite db file ，create it
       
        parent::onInited();
    }
    protected function checkDemoDb()
    {
        $dsn = $this->options['database_list'][0]['dsn']??null;
        if ($dsn !=='sqlite:demodb.db') {
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
        //MyCoverageBridge::_()->init($tester_options);
    }
}
