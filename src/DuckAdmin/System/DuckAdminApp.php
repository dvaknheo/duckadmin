<?php declare(strict_types=1);
/**
 * 这里我们做一下
 */
namespace DuckAdmin\System;


use DuckPhp\Core\Route;
use DuckPhp\DuckPhp;
use DuckPhp\FastInstaller\FastInstaller;

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
        'controller_method_prefix' => '',
        'class_admin'=> Admin::class,
        
        'cli_command_classes' => [FastInstaller::class],
        
        //----
        //'install_input_desc'=>'input [{abc}]',
        //'install_default_options'=>['abc'=>'def'],
    ];
    protected function onPrepare()
    {
        //默认的路由不符合我们这次的路由，换
        Route::_(ProjectRoute::_());
        parent::onPrepare();
    }
    protected function onInit()
    {
        //onInstall();  我们这里要安装
        //EventManager::OnEvent([static::class,'OnInstalled'],[static::class,'OnInstall']);
    }

    public static function OnInstalled()
    {
        return static::_()->_OnInstall();
    }
    public function _OnInstalled()
    {
        var_dump("welcome to SimpleBlog");
    }    
}
