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
    use AppPluginTrait { pluginModeInit as private _pluginModeInit; }
    
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
            'plugin_path_document' => 'res',
            'plugin_enable_readfile' =>true,
            'plugin_readfile_prefix' =>  'res/' ,
        ];

        $this->plugin_options['plugin_path'] = realpath(__DIR__.'/../').'/';
        $this->plugin_options['plugin_readfile_prefix'] = $this->plugin_options['duckadmin_resource_url_prefix'];
        $this->plugin_options = array_merge($ext_plugin_options, $this->plugin_options,);
        
        $this->options['path'] = $context->options['path'];
        $this->options['namespace'] = $context->options['namespace'];
        $this->options['table_prefix'] = $this->plugin_options['duckadmin_table_prefix'];
        $this->options['session_prefix'] = $this->plugin_options['duckadmin_session_prefix'];
        
        //$this->checkInstall(); //检查安装
        
        return $this->_pluginModeInit($plugin_options, $context);
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