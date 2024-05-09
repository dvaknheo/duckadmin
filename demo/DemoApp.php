<?php
use DuckPhp\DuckPhp;
use DuckPhp\Foundation\Helper;

class MainController
{
    public function action_index()
    {
        Helper::Show([],'main');
    }
}

class DemoApp extends DuckPhp
{
    public $options = [
        'is_debug' => true,
        'path' => __DIR__.'/',
        
        'cli_command_with_fast_installer' => true,
        'namespace_controller' => '\\',
        'app' => [
//*
            \DuckAdmin\System\DuckAdminApp::class => [      // 后台管理系统
                'controller_url_prefix' => 'app/admin/',    // 访问路径
                'controller_resource_prefix' => 'res/',     // 资源文件前缀
            ],
//*/
            \DuckUser\System\DuckUserApp::class => [
                'controller_url_prefix' => 'user/', // 访问路径
                'controller_resource_prefix' => '/res/user',  // 资源文件前缀
            ],
//*/
            \DuckUserManager\System\DuckUserManagerApp::class => [
                'controller_url_prefix' => 'app/admin/', // 访问路径
                'controller_resource_prefix' => '/res/app/admin/',  // 资源文件前缀
            ],
//*/
            \SimpleBlog\System\SimpleBlogApp::class => [
                'controller_url_prefix' => 'blog/', // 访问路径
                'controller_resource_prefix' => '/res/blog',  // 资源文件前缀
            ],
//*/
        ],
    ];
    public function onInit()
    {
        // You Codes,
    }
    public function command_foo($uri = '', $post = false)
    {
        //var_dump(Console::_()->getCliParameters());
    }
}
