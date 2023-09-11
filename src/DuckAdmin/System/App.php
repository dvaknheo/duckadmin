<?php declare(strict_types=1);
/**
 * 这里我们做一下
 */
namespace DuckAdmin\System;

use DuckPhp\Component\DbManager;
use DuckPhp\Core\Route;
use DuckPhp\DuckPhp;

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
        'ext_options_from_config' => true,
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
        $this->switchDbManager();
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
        $options = DbManager::G()->options;
        if(empty($options['database']) || empty($options['database_list'])){
            $post = $this->options['database'] ?? null;
            if(!empty($post)){
                DbManager::G()->options['database']=$post;
                DbManager::G()->options['force']=true;

                DbManager::G( )->init(DbManager::G()->options, static::Root());
            }
        }
    }
}
