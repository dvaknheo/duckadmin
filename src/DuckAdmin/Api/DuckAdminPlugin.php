<?php declare(strict_types=1);
/**
 * DuckPhp
 * From this time, you never be alone~
 */

namespace DuckAdmin\Api;

use DuckAdmin\System\App;
use DuckPhp\Component\AppPluginTrait;

/**
 * 这是继承了DuckAdmin 入口类的插件类
 * $options['ext'][\DuckAdmin\Api\DuckAdminPlugin::class] = [
 *    duckadmin_installed' => false,
 *    //...
 * ];
 */
class DuckAdminPlugin extends App
{
    use AppPluginTrait {
        // 覆盖
        pluginModeInit as private _pluginModeInit;
        onPluginModeBeforeRun as private _onPluginModeBeforeRun;
    }
    
    // 可调的外部设置
    public $plugin_options = [
            'plugin_search_config'  => false,
            
            'duckadmin_installed' => false, // 是否已经安装
            'duckadmin_resource_url_prefix' => 'res/', //资源前缀
            'duckadmin_table_prefix' => '',       // 表前缀
            'duckadmin_session_prefix' => '',     // session 前缀
    ];
    //////  初始化
    public function pluginModeInit(array $plugin_options, object $context = null)
    {
        // 这里的配置是内部配置
        $ext_plugin_options = [
            // 'plugin_readfile_prefix' =>  'res/' ,
            'plugin_path_document' => 'res',
            'plugin_enable_readfile' =>true,
            'plugin_init_override_to_options'=> true,
            'plugin_route_options' => [
                // 这里要把 controller_base_class 加回去
            ],
        ];

        $this->plugin_options['plugin_path'] = realpath(__DIR__.'/../').'/';
        $this->plugin_options['plugin_readfile_prefix'] = $this->plugin_options['duckadmin_resource_url_prefix'];
        $this->plugin_options = array_merge($ext_plugin_options, $this->plugin_options,);
        
        $ret = $this->_pluginModeInit($plugin_options, $context);
        //我们还要处理数据库链接？
        $this->options['table_prefix'] = $this->plugin_options['duckadmin_table_prefix'];
        $this->options['session_prefix'] = $this->plugin_options['duckadmin_session_prefix'];
        
        return $ret;
    }
    //
    protected function onPluginModeBeforeRun()
    {
        //$this->checkInstall(); // 检查安装，不能在初始化里
        return $this->_onPluginModeBeforeRun();
    }
    /////////////////////////////////////////
    public static function Action()
    {
        return DucckAdminAction::G();
    }
    public static function Service()
    {
        return DucckAdminService::G();
    }
}