<?php declare(strict_types=1);
/**
 * DuckPHP
 * From this time, you never be alone~
 */
namespace DuckUser\System;

use DuckPhp\DuckPhp;
use DuckPhp\Ext\InstallableTrait;

class App extends DuckPhp
{
    //@override
    public $options = [
		'controller_class_postfix' => 'Controller',
        
        'table_prefix' => 'duckuser_',   // 表前缀
        'session_prefix' => '',  // Session 前缀
        
        //// 以下是独有选项
        'home_url' => 'Home/index', 
    ];

    protected function onInit()
    {
        //$this->checkInstall();
    }
}
