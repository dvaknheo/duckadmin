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
use DuckCoverage\DuckCoverage;
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
    protected function onPrepare(): void
    {
        parent::onPrepare();
        if (static::Setting('duckadmin_demo_enable_test') || $this->options['duckadmin_demo_enable_test'] ?? false) {
            $this->options['data_file_json_file'] = 'DuckPhpData-test.config.json';
        }
    }

    public function onInited(): void
    {
        // workermanhttpd 不再支持（包已移除），如需 workerman 支持请恢复依赖
        if (static::Setting('duckadmin_demo_enable_test') || $this->options['duckadmin_demo_enable_test']??false) {
            $this->enableTest();
        }
       
        parent::onInited();
    }

    /**
     * override 根应用 serve():在请求前后触发 DuckCoverage 的 web 覆盖收集
     */
    public function serve(): bool
    {
        $bridge = \DuckCoverage\DuckCoverage::_();
        $bridge->_OnBeforeRun();
        $ret = parent::serve();
        $bridge->_OnAfterRun();
        return $ret;
    }

    protected function enableTest()
    {
        // for coverage test
        $path_src = realpath(__DIR__.'/../../src/').'/';
        $tester_options = [
            'duckcoverage_path_src'=> $path_src,
            'duckcoverage_callback_class'=> MyTester::class,
            
            'duckcoverage_server_port'=> 8080,
            'duckcoverage_homepage' =>'/index.php/',
            'duckcoverage_path_document'=>'public',
            'duckcoverage_new_server'=>true,
            'duckcoverage_web_base_url' => 'http://admin.duckphp-local.com/',
        ];
        DuckCoverage::_()->init($tester_options);
    }
}
