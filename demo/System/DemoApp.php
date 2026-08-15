<?php declare(strict_types=1);
/**
 * DuckPhp
 * From this time, you never be alone~
 */
namespace DuckAdminDemo\System;


use DuckPhp\DuckPhp;
use DuckPhp\Foundation\Controller\Helper;

use DuckCoverage\DuckCoverage;

use DuckAdminDemo\System\TestLister;
use DuckAdmin\Admin\System\AdminApp;
use DuckAdmin\User\System\UserApp;

class DemoApp extends DuckPhp
{
    public $options = [
        
        'path' => __DIR__.'/../',
        'namespace' => 'DuckAdminDemo',
        
        'data_file_enable' => true,
        
        'controller_resource_prefix' => '/',  //for workerman local file
        'path_resource' => 'public',          //for workerman local file
        
        'app' => [
//*
            AdminApp::class => [
                'controller_url_prefix' => 'admin/',
                'controller_resource_prefix' => 'res/',
            ],
//*/
            UserApp::class => [
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
        'ext'=> [
            DuckCoverage::class => true,
        ],

        'duckcoverage_enable' => true,
        'duckcoverage_group'=>'',
        'duckcoverage_name'=>'',

        'duckcoverage_save_web_request_list' =>true,
        'duckcoverage_save_local_call_list' =>false,

        //'duckcoverage_report_direct'=>true,
        //'duckcoverage_echo_back'=>false,
        
        'duckcoverage_path_src'=> null,  //$path_src,
        'duckcoverage_callback'=> [TestLister::class ,'GetTestList'],

        'duckcoverage_web_base_url' => 'http://admin.duckphp-local.com/',
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
        $data['url_user'] = __url(\DuckAdmin\User\System\UserApp::_()->options['controller_url_prefix']) . 'index';
        $data['url_admin'] = __url(\DuckAdmin\Admin\System\AdminApp::_()->options['controller_url_prefix']) . 'index';
        
        $data ['duckadmin_demo_enable_test'] = $this->options['duckadmin_demo_enable_test'] ?? false;
        
        Helper::Show($data,'main');
    }

    protected function onPrepare(): void
    {
        DuckCoverage::_()->beforeInit(); //mover  json data ;
        parent::onPrepare();
        // something from setting;
    }
}
