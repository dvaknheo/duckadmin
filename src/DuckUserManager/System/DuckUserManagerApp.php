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
        'controller_resource_prefix' => 'res/',  // 资源文件前缀
        
        'ext' =>[
            RouteHookResource::class => true
        ],
        
        //'class_admin'=> Admin::class,
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

}