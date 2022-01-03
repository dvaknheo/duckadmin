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
        'controller_base_class' => ProjectController::class, // 限定死在 
        
        'home_url' => 'Home/index', // 登录页
        'table_prefix' => 'user_',   // 表前缀
        'session_prefix' => '',  // Session 前缀
    ];
    
    public function __construct()
    {
        parent::__construct();
    }
    protected function onBeforeRun()
    {
        //$this->checkInstall();
    }
    //////////////////////
    public function command_install()
    {
        echo "welcome to Use SimplAuth installer  --force  to force install\n";
        $parameters =  static::Parameter();
        if(count($parameters)==1 || ($parameters['help'] ?? null)){
            // echo "--force  to force install ;";
            //return;
        }
        echo $this->install($parameters);
        echo "Done \n";
    }
    public function install($parameters)
    {
        $options = [
            'force' => $parameters['force']?? false,
            'path' => $this->getPath(),
            
            'sql_dump_prefix' => '',
            'sql_dump_inlucde_tables' => [ 'Users'],        
            'sql_dump_install_replace_prefix' => true,
            'sql_dump_install_new_prefix' => $this->options['simple_auth_table_prefix'],
            'sql_dump_install_drop_old_table' => $parameters['force']?? false,
        ];
        Installer::G()->init($options,$this);
        
        echo Installer::G()->run();
    }
}
