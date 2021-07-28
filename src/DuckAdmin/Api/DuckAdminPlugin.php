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
    public $is_plugin = false;
    
    public $plugin_options = [
        'plugin_path_document' => 'res',
        'plugin_enable_readfile' =>true,
        'plugin_readfile_prefix' => '/res',
    ];
    public function __construct()
    {
        $path=realpath(__DIR__.'/../../../').'/';
        parent::__construct();
        $this->plugin_options['plugin_path'] = $path;
        $this->plugin_options['plugin_search_config'] = false;        
    }
    protected function onPluginModeInit()
    {
        $this->is_plugin = true;
        App::G(static::G());
        
        //copy options
        foreach($this->options as $k => $v){
            if(isset($this->plugin_options[$k])){
                $this->options[$k]= $this->plugin_options[$k];
            }
        }
        
        Console::G()->regCommandClass(static::class,  'SimpleAuth');
    }
    protected function onPluginModeBeforeRun()
    {
        //$this->checkInstall();
    }
    public function getPath()
    {
        return $this->plugin_options['plugin_path'];
    }
    public static function RunAsPlugin($options, $plugin_options = [])
    {
        $options['ext'][static::class] = $plugin_options;
        return DuckPhp::RunQuickly($options);
    }
}