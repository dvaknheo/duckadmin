<?php declare(strict_types=1);
/**
 * DuckPHP
 * From this time, you never be alone~
 */
namespace DuckUser\Api;

use DuckPhp\Component\AppPluginTrait;
use DuckPhp\Component\Console;
use DuckUser\System\App;

class DuckUserPlugin extends App
{
    use AppPluginTrait {
        // 覆盖
        pluginModeInit as private _pluginModeInit;
        onPluginModeBeforeRun as private _onPluginModeBeforeRun;
    }
    
    // 可调的外部设置
    public $plugin_options = [
            'controller_resource_prefix' => 'res/', //资源前缀
    ];
    //////  初始化
    public function pluginModeInit(array $plugin_options, object $context = null)
    {
        // 这里的配置是内部配置
        $ext_plugin_options = [
            'plugin_path_document' => 'res/',
            'plugin_enable_readfile' =>true,
        ];
        // 这里
        $this->plugin_options['plugin_path'] = realpath(__DIR__.'/../').'/'; // 节约性能，不搜索
        $this->plugin_options['plugin_search_config'] = false; // 节约性能，不搜索
        $this->plugin_options['plugin_readfile_prefix'] = $this->options['controller_resource_prefix']; // 这里可能要改。
        $this->plugin_options['plugin_route_options']['controller_base_class'] = $this->options['controller_base_class']; // 拉配置那边的过来。
        
        $this->plugin_options = array_merge($ext_plugin_options, $this->plugin_options);
        $ret = $this->_pluginModeInit($plugin_options, $context);
        return $ret;
    }
    protected function onPluginModeBeforeRun()
    {
        //$this->checkInstall(); // 检查安装，不能在初始化里
        return $this->_onPluginModeBeforeRun();
    }
    ////////////////////////
    public function getPath()
    {
        return $this->plugin_options['plugin_path'];
    }
}