<?php
use DuckPhp\DuckPhp;
use DuckPhp\Foundation\CommonCommandTrait;
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
    use CommonCommandTrait;
    use FastInstallerTrait;
    
    public $options = [
        'path' => __DIR__.'/',
        'is_debug' => true,
        
        'cli_command_class' => null,
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
        'install_options'=>[
            'app_a'=>'111',
        ],
        //install_need_redis
        //install_need_database
        //install_options
        //install_input_validators
        'install_input_desc' => <<<EOT
url prefix: [{controller_url_prefix}]
resource prefix: [{controller_resource_prefix}]
zzzzz [{app_a}]
zzzzz [{app_b}]
//就是说duck可以很好的解决应用间的复用和嵌套问题，而且对应用的修改和拓展也是无侵害的
----
EOT
,
        'app_b'=> '222',
    ];
}
