<?php declare(strict_types=1);
/**
 * ，这里我们做一下
 */

namespace DuckAdmin\System
{

use DuckPhp\DuckPhp;

use DuckPhp\Ext\InstallableTrait;

/**
 * 入口类
 */
class App extends DuckPhp
{
    use InstallableTrait;
    //@override
    public $options = [
        'error_404' => '_sys/error_404',
        'error_500' => '_sys/error_500',
        'controller_resource_prefix' => 'res/',  // 资源文件前缀
		'controller_class_postfix' => 'Controller',
    ];
    public function onInit()
    {
        //Route 替换。
        ProjectRoute::G()->init(Route::G()->options,$this);
        Route::G(ProjectRoute::G());
    }
    /////////
    public static function ActionApi()
    {
        return ActionApi::G();
    }
    public static function ServiceApi()
    {
        return ServiceApi::G();
    }
    ////////////// 命令行
    public function command_install()
    {
        // 安装命令。
        echo "welcome to Use DuckAdmin installer  --force  to force install\n";
        $parameters =  static::Parameter();
        if(count($parameters)==1 || ($parameters['help'] ?? null)){
            // echo "--force  to force install ;";
            //return;
        }
        echo $this->install($parameters); // on InstallableTrait
        echo "Done \n";
    }
	protected function switchDbManager()
	{
        // 这里旧的数据库配置
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
/////////////
}