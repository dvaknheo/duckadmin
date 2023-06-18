<?php declare(strict_types=1);
/**
 * DuckPhp
 * From this time, you never be alone~
 */

namespace DuckAdmin\Api;

use DuckAdmin\System\App;
use DuckAdmin\System\ProjectRoute;

use DuckPhp\Core\App as DuckPhpCoreApp;
use DuckPhp\Component\AppPluginTrait;
use DuckPhp\Component\DbManager;
use DuckPhp\Core\Route;
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
    }
    // 可调的外部设置， 声明
    public $plugin_options = [
            'plugin_path_document' => 'res/',
            'plugin_enable_readfile' =>true,
			
			'plugin_component_class_route'=>ProjectRoute::class,
			'plugin_search_config' =>false,
			'plugin_files_config' => [
				'pear_config',
				'menu',
				'database',
			],
    ];
	
    /////////////////////////////////////////
    public static function Action()
    {
        return DuckAdminAction::G();
    }
    public static function Service()
    {
        return DuckAdminService::G();
    }
    //////  初始化
    public function pluginModeInit(array $plugin_options, object $context = null)
    {
        // 这里的配置是内部配置
        $ext_plugin_options = [

        ];
        // 这里
        $this->plugin_options['plugin_path'] = realpath(__DIR__.'/../').'/'; // 节约性能，不搜索
		
        $this->plugin_options['plugin_readfile_prefix'] = $this->options['controller_resource_prefix']; // 这里可能要改。
        $this->plugin_options['plugin_route_options']['controller_base_class'] = $this->options['controller_base_class'] ?? ''; // 拉配置那边的过来。
        $this->plugin_options['plugin_route_options']['controller_class_postfix'] = $this->options['controller_class_postfix'] ?? ''; // 拉配置那边的过来。
		//		'controller_class_postfix' => 'Controller',

        $this->plugin_options = array_merge($ext_plugin_options, $this->plugin_options);
		
		
        return $this->_pluginModeInit($plugin_options, $context);
    }
    //
    protected function onPluginModeBeforeRun()
    {
		$this->switchDbManager();
        static::FireEvent([static::class, __FUNCTION__]);
		//return $this->_onPluginModeBeforeRun();
    }
	protected function switchDbManager()
	{
		$old = DbManager::G();
		$options = $old->options;
		if(empty($options['database']) || empty($options['database_list'])){
			$connections = static::Config('connections',[],'database');
			$post = $connections['mysql']??null;
			if(!empty($post)){
				DbManager::G()->options['database']=[
					'dsn'=>"mysql:host={$post['host']};port={$post['port']};dbname={$post['database']};charset=utf8mb4;",
					'username'=>$post['username'],	
					'password'=>$post['password'],
				];
				DbManager::G()->init(DbManager::G()->options, DuckPhpCoreApp::G());
			}
		}
	}
    

}