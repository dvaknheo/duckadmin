<?php
use DuckPhp\DuckPhp;
use DuckPhp\Foundation\FastInstallerTrait;
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
    use FastInstallerTrait;
    
    public $options = [
        'path' => __DIR__.'/',
        
        'is_debug' => true,
        
        'namespace_controller' => '\\',
        
        'ext' => [
            // add your extensions;
        ],
        'app' => [
            // 后台管理系统
//*
            \DuckAdmin\System\DuckAdminApp::class => [
                'controller_url_prefix' => 'app/admin/', // 访问路径
                'controller_resource_prefix' => 'res/',  // 资源文件前缀
            ],
//*/
            \DuckUser\System\DuckUserApp::class => [
                'controller_url_prefix' => 'user/', // 访问路径
                'controller_resource_prefix' => 'res/',  // 资源文件前缀
            ],
//*/
            \DuckUserManager\System\DuckUserManagerApp::class => [
                'controller_url_prefix' => 'app/admin/', // 访问路径
                'controller_resource_prefix' => 'res/',  // 资源文件前缀
            ],
//*/
            \SimpleBlog\System\SimpleBlogApp::class => [
                'controller_url_prefix' => 'blog/', // 访问路径
                'controller_resource_prefix' => 'res/',  // 资源文件前缀
            ],
//*/
        ],
        
    ];
}
