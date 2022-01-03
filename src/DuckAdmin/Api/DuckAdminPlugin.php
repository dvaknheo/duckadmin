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
    
    // 可调的外部设置， 声明
    public $plugin_options = [
            'duckadmin_resource_url_prefix' => 'res/', //资源前缀
            // 'controller_resource_prefix'
            // 'controller_base_class'
    ];
    //////  初始化
    public function pluginModeInit(array $plugin_options, object $context = null)
    {
        $this->plugin_options['plugin_path'] = realpath(__DIR__.'/../').'/'; // 节约性能，不搜索
        $this->plugin_options['plugin_search_config'] = false; // 节约性能，不搜索
        $this->plugin_options['plugin_route_options']['controller_base_class'] = $this->options['controller_base_class']; // 拉配置那边的过来。
        
        $this->plugin_options = array_merge($ext_plugin_options, $this->plugin_options);
        $ret = $this->_pluginModeInit($plugin_options, $context);
        return $ret;
    }
    //
    protected function onPluginModeBeforeRun()
    {
        //$this->checkInstall();
        return $this->_onPluginModeBeforeRun();
    }
    
    // 后两个是通用的方法，只留一个入口
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