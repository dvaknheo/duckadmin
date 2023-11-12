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
//use DuckAdmin\System\ProjectRoute;


/**
 * 入口类
 */
class DuckUserManagerApp extends DuckPhp
{
    //@override
    public $options = [
        'is_debug' => true,
        'ext_options_from_config' => true,  //使用额外的选项

        'controller_class_postfix' => 'Controller', // 控制器后缀
        'controller_resource_prefix' => 'res/',  // 资源文件前缀
        
        'ext' =>[
            RouteHookResource::class => true
        ],
        
        //'class_admin'=> AdminApi::class,
        
        'exception_project'=> ProjectException::class,
        'exception_business'=> ProjectException::class,
        'exception_controller'=> ProjectException::class,
        
        'mode' => 'default',
    ];
    public function __construct()
    {
        $this->options['path'] = dirname(__DIR__).'/';
        parent::__construct();
    }
    public function onPrepare()
    {
        //默认的路由不符合我们这次的路由，还过
        Route::_(ProjectRoute::_());
    }
    public function onInit()
    {
        // 两种模式 1 自己的相位空间里全干
        // 2 作为 在 duckadmin 路由插件的相位搞 -> 牵扯太多。还是老老实实搞自己的
        if($this->options['mode']==='route'){
            $myRoute = Route::_();
            $myRes = RouteHookResource::_(); //
            static::PhaseCall(get_class(static::Root()),function()use($myRoute){
                // 路由模式下，资源文件 的问题, //还有他那边路由不兼容自己家的问题
                // view 文件的问题 —— view 
                // 我们要成功才切换 view 文件。 后面又得切回来，所以都不行
                Route::_()->add404RouteHook([$myRoute,'run']);
            });
            return;
        }
        //其他情况
    }
    public function run():bool
    {
        if($this->options['mode']==='route'){
            return false;
        }
        return parent::run();
    }

}