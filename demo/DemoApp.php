<?php
use DuckPhp\DuckPhp;

class DemoApp extends DuckPhp
{
    public $options = [
        'is_debug' => true,
        'path' => __DIR__.'/',
        'ext' => [
            // 后台管理系统
//*/
            \DuckAdmin\System\DuckAdminApp::class => [
                'controller_url_prefix' => 'app/admin/', // 访问路径
                'controller_resource_prefix' => 'res/',  // 资源文件前缀
            ],
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
        'namespace_controller' => '\\',
    ];
    public function command_install()
    {
        // install database
        //stripcslashes
        
    }
    //@Override
    public function install($options, $parent_options =[])
    {
        //
    }
    protected function installApps()
    {
        foreach($this->options['ext'] as $app => $options){
            if(!is_a($app,\DuckPhp\Core\App::class)){ continue;}
            //echo "install xx"
        }
    }
    protected function configDatabase()
    {
        //get database setting from me, or setting.
        $desc = <<<EOT
DatabaseSetting
host[{host}]
port[{port}]

areyousure[{ok}]

done;

EOT;
        $options=[
            //'host'=>'127.0.0.1',
            'port'=>'80',
        ];
    }
    protected function configRedis()
    {
    }
}
class MainController
{
    public function action_index()
    {
        \DuckPhp\Foundation\Helper::Show([],'main');
    }
}