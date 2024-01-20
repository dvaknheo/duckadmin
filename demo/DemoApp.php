<?php
use DuckPhp\DuckPhp;
class DemoApp extends DuckPhp
{
    public $options = [
        'is_debug' => true,
        'path' => __DIR__.'/',
        'ext_options_file_enable'=>true,
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
    /**
     * Install
     */
    public function command_install($force=false)
    {
        $flag = MySqlDatabaseSetter::_()->callResetDatabase($force);
        if(!$flag){
            return;
        }
        $this->install([],[]);
        echo "Install Main Done , install ext Apps\n";
        foreach($this->options['ext'] as $app => $options){
            if(!is_a($app,\DuckPhp\Core\App::class)){ continue;}
            if(method_exist($app,'command_install')){
                $app::_()->command_install($force);
            }
        }
        echo "Install All Done \n";
        return;
    }
    /**
     * Config
     */
    public function command_config($force = false)
    {
        MySqlDatabaseSetter::_()->callResetDatabase($force);
    }

}
class MainController
{
    public function action_index()
    {
        \DuckPhp\Foundation\Helper::Show([],'main');
    }
}