<?php declare(strict_types=1);
/**
 * 这里我们做一下
 */
namespace DuckAdmin\System;

use DuckPhp\DuckPhp;

use DuckPhp\Core\Route;

/**
 * 入口类
 */
class App extends DuckPhp
{
    //@override
    public $options = [
        'is_debug' => true,
        'error_404' => '_sys/error_404',
        'error_500' => '_sys/error_500',
        'controller_class_postfix' => 'Controller', // 控制器后缀
        'controller_resource_prefix' => '/res/',  // 资源文件前缀
        //'path' => dirname(__DIR__),
    ];
    public function __construct()
    {
        $this->options['path']=dirname(__DIR__).'/';
        parent::__construct();
    }
    public function onInit()
    {
        //替换掉默认的路由，这里牺牲了点效率
        ProjectRoute::G()->init(Route::G()->options,$this);
        Route::G(ProjectRoute::G());
    }
    /////////
    public static function ActionApi()
    {
        // 这里应该采用的代理模式
        return ActionApi::G();
    }
    public static function ServiceApi()
    {
        // 这里应该采用的代理模式
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
        //echo $this->install($parameters); // 一些安装动作，这里还没想好
        echo "Done \n";
    }
    protected function _install()
    {
        //安装和强制安装的逻辑
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
