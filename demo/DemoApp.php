<?php declare(strict_types=1);
/**
 * DuckPhp
 * From this time, you never be alone~
 */
namespace Demo;

use DuckPhp\DuckPhp;
use DuckPhp\Foundation\Controller\Helper;

class DemoApp extends DuckPhp
{
    public $options = [
      'is_debug' => true,
        'path' => __DIR__.'/',
        'cli_command_with_fast_installer' => true,  //for install command
        'app' => [
//*
            \DuckAdmin\System\DuckAdminApp::class => [      // 后台管理系统
                'controller_url_prefix' => 'app/admin/',    // 访问路径
                'controller_resource_prefix' => 'res/',     // 资源文件前缀
                //'controller_resource_prefix' => '/res/app/admin/',     // 资源文件前缀
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
        // embed welcomepage
        $path = explode('\\', __CLASS__);
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
        // route: /  just show page;
        Helper::Show([],'main');
    }
    public function onInit()
    {
        parent::onInit();
        // You Codes,
    }
    
    public function command_hello()
    {
        // show a command demo
        echo "From this time, you never be alone~\n";
    }
}
