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
    use AppPluginTrait;
    
    
    // 可调的外部设置
    public $plugin_options = [
            'duckadmin_installed' => false, // 是否已经安装
            'duckadmin_resource_url_prefix' => 'res/', //资源前缀
            
            'table_prefix' => '',       // 表前缀
            'session_prefix' => '',     // session 前缀
    ];
    public function __construct()
    {
        parent::__construct();
        // 这里的配置是内部配置。
        $ext_plugin_options = [
            'plugin_search_config'  => false, //
            
            
            'plugin_path_document' => 'res',
            'plugin_enable_readfile' =>true,
            'plugin_readfile_prefix' =>  'res/' ,
        ];
        $this->plugin_options['plugin_path'] = realpath(__DIR__.'/../').'/'; //TODO 这句看怎么取消
        $this->plugin_options['plugin_readfile_prefix'] = $this->plugin_options['duckadmin_resource_url_prefix'];

        $this->plugin_options = array_merge($ext_plugin_options, $this->plugin_options);
        
        // 我们找一条融合 $options 和 $plugin_options 的初始方法？
    }
    /////////////////////////////////////////
    public function pluginModeInit(array $plugin_options, object $context = null)
    {
        $ret = parent::pluginModeInit($plugin_options, $context);
        return $ret;
    }
    protected function onPluginModeBeforeRun()
    {
        $this->plugin_options['plugin_readfile_prefix'] = $this->plugin_options['duckadmin_resource_url_prefix']; //这里要调
        
        //$this->checkInstall(); // 检查安装
        return parent::onPluginModeBeforeRun();
    }
    ///////////////////
    //以下是固定标配
    public static function Action()
    {
        return DuckAdminAction::G();
    }
    public static function Service()
    {
        return DuckAdminService::G();
    }
}