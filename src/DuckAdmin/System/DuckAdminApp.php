<?php declare(strict_types=1);
/**
 * 这里我们做一下
 */
namespace DuckAdmin\System;

use DuckPhp\Core\Route;
use DuckPhp\DuckPhp;
/**
 * 入口类
 */
class DuckAdminApp extends DuckPhp
{
   
    //@override
    public $options = [
        'path' => __DIR__ . '/../',
        'controller_resource_prefix' => 'res/',  // 资源文件前缀
        'database_driver'=>'mysql',
        
        'cli_command_class' => Command::class,
        'class_admin'=> Admin::class,
        //----
        //'install_input_desc'=>'input [{abc}]',
        //'install_default_options'=>['abc'=>'def'],
    ];
    protected function onPrepare()
    {
        //默认的路由不符合我们这次的路由，换
        Route::_(ProjectRoute::_());
    }
}
