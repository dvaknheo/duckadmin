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
            'plugin_path_document' => 'res',
            'plugin_enable_readfile' =>true,
            //'plugin_readfile_prefix' =>  'res/' ,
            'plugin_search_config'  => false,
        ];
        $this->plugin_options['plugin_readfile_prefix'] = $this->plugin_options['duckadmin_resource_url_prefix'];

        $this->plugin_options['plugin_path'] = realpath(__DIR__.'/../').'/';
        $this->plugin_options = array_merge($ext_plugin_options, $this->plugin_options);
    }
    /////////////////////////////////////////
    protected function onPluginModeBeforeRun()
    {
        $this->plugin_options['plugin_readfile_prefix'] = $this->plugin_options['duckadmin_resource_url_prefix'];
        //$this->checkInstall(); // 检查安装
    }
    
    public static function Action()
    {
        return DucckAdminAction::G();
    }
    public static function Service()
    {
        return DucckAdminService::G();
    }
}