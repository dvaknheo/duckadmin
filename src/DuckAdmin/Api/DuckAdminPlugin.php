<?php declare(strict_types=1);
/**
 * DuckPhp
 * From this time, you never be alone~
 */

namespace DuckAdmin\Api;

use DuckAdmin\System\App;
use DuckPhp\Component\AppPluginTrait;
use DuckPhp\Component\Console;
/**
 * 这是继承了DuckAdmin 入口类的插件类
 * $options['ext'][\DuckAdmin\Api\DuckAdminPlugin::class] = [
 *   // 你要添加的选项
 * ];
 */
class DuckAdminPlugin extends App
{
    use AppPluginTrait;
    
    // 可调的外部设置
    public $plugin_options = [
            'duckadmin_check_installed' => true,
            'duckadmin_resource_url_prefix' => '/res',
            
            'duckadmin_table_prefix' => '',
            'duckadmin_session_prefix' => '',
    ];
    public function __construct()
    {
        parent::__construct();
        parent::G($this); // 这句有问题
        
        // 这里的配置是内部配置。
        $ext_plugin_options = [
            'plugin_path_document' => 'res',
            'plugin_enable_readfile' =>true,
            'plugin_readfile_prefix' => ,
            'plugin_search_config'  => false,
        ];
        $this->plugin_options['plugin_readfile_prefix'] = $this->plugin_options['duckadmin_resource_url_prefix'];
        
        $this->plugin_options = array_merge($ext_plugin_options, $this->plugin_options);
    }
    /////////////////////////////////////////
    protected function onPluginModeBeforeRun()
    {
        $this->checkInstall();
    }
    ////////////////////////////////////
    public static function RunAsPlugin($options, $plugin_options = [])
    {
        $options['ext'][static::class] = $plugin_options;
        return DuckPhp::RunQuickly($options);
    }
}