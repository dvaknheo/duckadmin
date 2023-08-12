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
    use InstallableTrait;
    //@override
    public $options = [
		'controller_class_postfix' => 'Controller',
        
        'table_prefix' => 'duckuser_',   // 表前缀
        'session_prefix' => '',  // Session 前缀
        
        //// 以下是独有选项
        'home_url' => 'Home/index', 
    ];

    protected function onBeforeRun()
    {
        //$this->checkInstall();
    }
    //////////////////////
    public function command_install()
    {
        echo "welcome to Use DuckUser installer  --force  to force install\n";
        $parameters =  static::Parameter();
        if(count($parameters)==1 || ($parameters['help'] ?? null)){
            // echo "--force  to force install ;";
            //return;
        }
        echo $this->install($parameters);
        echo "Done \n";
    }
}
