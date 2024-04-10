<?php declare(strict_types=1);
/**
 * 这里我们做一下
 */
namespace DuckAdmin\System;

use DuckPhp\Core\Route;
use DuckPhp\DuckPhp;
use DuckPhp\Foundation\CommonCommandTrait;
use DuckPhp\Foundation\FastInstallerTrait;

/**
 * 入口类
 */
class DuckAdminApp extends DuckPhp
{
    use CommonCommandTrait;
    use FastInstallerTrait;
    
    //@override
    public $options = [
        'path' => __DIR__ . '/../',
        'controller_method_prefix' => '', // 控制器后缀
        'controller_resource_prefix' => 'res/',  // 资源文件前缀
        'ext_options_file_enable' => true,  //使用额外的选项
        'cli_command_class' => null,

        'class_admin'=> Admin::class,
        //----
        //'install_input_desc'=>'input [{abc}]',
        //'install_default_options'=>['abc'=>'def'],
        
        'sql_dump_include_tables_all' => false,
        'sql_dump_include_tables_by_model' => true,
        
    ];
    protected function onPrepare()
    {
        //默认的路由不符合我们这次的路由，换
        Route::_(ProjectRoute::_());
    }
}
