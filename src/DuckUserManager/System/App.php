<?php declare(strict_types=1);
/**
 * 这里我们做一下
 */
namespace DuckUserManager\System;

use DuckPhp\Component\DbManager;
use DuckPhp\Component\RouteHookResource;

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
        'controller_class_postfix' => 'Controller', // 控制器后缀
        'controller_resource_prefix' => 'res/',  // 资源文件前缀
        'ext_options_from_config' => true,
        'ext' =>[
            //RouteHookResource::class => true
        ],
        
        'class_session'=> Session::class,
        'class_user'=> ActionApi::class,
        
        'exception_project'=> ProjectException::class,
        'exception_business'=> ProjectException::class,
        'exception_controller'=> ProjectException::class,
    ];
    public function __construct()
    {
        $this->options['path'] = dirname(__DIR__).'/';
        parent::__construct();
    }
    public function onInit()
    {
        // 切换路由
        $this->switchRoute(ProjectRoute::class);
        
        //如果根应用没设置数据库，用自己的
        $this->switchDbManager();
    }
    protected function switchRoute($class)
    {
         //为了满足 webman admin 的路由 替换掉默认的路由，这里牺牲了点效率
        $class::G()->init(Route::G()->options, $this);
        
        //切换路由之后，路由钩子会重置
        $class::G()->pre_run_hook_list = Route::G()->pre_run_hook_list;
        $class::G()->post_run_hook_list = Route::G()->post_run_hook_list;
        Route::G($class::G());
    }
}