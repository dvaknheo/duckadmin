<?php declare(strict_types=1);
/**
 * 这里我们做一下
 */
namespace DuckUserManager\System;

use DuckPhp\Component\DbManager;
use DuckPhp\Component\RouteHookResource;
use DuckPhp\Core\Route;
use DuckPhp\DuckPhp;

use DuckAdmin\System\App as DuckAdmin;
use DuckAdmin\System\AdminApi;
use DuckAdmin\System\ProjectException;
use DuckAdmin\System\ProjectRoute;


/**
 * 入口类
 */
class App extends DuckPhp
{
    //@override
    public $options = [
        'controller_class_postfix' => 'Controller', // 控制器后缀
        'controller_resource_prefix' => 'res/',  // 资源文件前缀
        'ext' =>[
            RouteHookResource::class => true
        ],
        
        'class_admin'=> AdminApi::class,
        
        'exception_project'=> ProjectException::class,
        'exception_business'=> ProjectException::class,
        'exception_controller'=> ProjectException::class,
        
        'mode' = 'route'
    ];
    public function __construct()
    {
        $this->options['path'] = dirname(__DIR__).'/';
        parent::__construct();
    }
    public function onPrepare()
    {
        //我们还是用 Duckadmin 的路由
        //Route::G();
    }
    public function onInit()
    {
        // 两种模式 1 自己的相位空间里全干
        // 2 作为 在 duckadmin 路由插件的相位搞
        if($this->options['mode']==='route'){
            $myRoute = Route::_();
            $myRes = RouteHookResource::_();
            static::PhaseCall(get_class(static::Root()),function()use($myRoute){
                // 路由模式下，资源文件 的问题
                RouteHookResource::_($myRes);
                Route::_()->addRouteHook($myRoute);
            });
            return;
        }
        //其他情况
    }
    public function run()
    {
        if($this->options['mode']==='route'){
            return false;
        }
        return parent::run();
    }

}