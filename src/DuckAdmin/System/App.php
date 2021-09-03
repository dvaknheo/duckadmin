<?php declare(strict_types=1);
/**
 * DuckPhp
 * From this time, you never be alone~
 */

namespace DuckAdmin\System;

use DuckPhp\DuckPhp;

use DuckCommon\Installer;

/**
 * 入口类
 */
class App extends DuckPhp
{
    //@override
    public $options = [
        'error_404' => '_sys/error_404',
        'error_500' => '_sys/error_500',
        
        'duckadmin_installed' => false,  // 检查安装
        'duckadmin_table_prefix' => '',   // 表前缀
        'duckadmin_session_prefix' => '',  // Session 前缀 
    ];
    protected function onBeforeRun()
    {
        $this->checkInstall();
    }
    protected function checkInstall()
    {
        if ($this->options['duckadmin_installed'] || static::Setting('duckadmin_installed') ){
            return;
        }
        $has_database = (static::Setting('database') ||  static::Setting('database_list')) ? true : false;
        
        Installer::CheckInstall([],$this , $has_database);
    }
    //////////////////////
    public function install($parameters)
    {
        $options = [
            'force' => $parameters['force']?? false,
            'installer_table_prefix' => $this->options[ 'duckadmin_table_prefix'],
        ];
        
        return Installer::G()->init($options,$this)->run();
    }
    ////////////// 命令行
    public function command_install()
    {
        echo "welcome to Use DuckAdmin installer  --force  to force install\n";
        $parameters =  static::Parameter();
        if(count($parameters)==1 || ($parameters['help'] ?? null)){
            // echo "--force  to force install ;";
            //return;
        }
        echo $this->install($parameters);
        echo "Done \n";
    }
    /////////////////////
    // 表格前缀， session 前缀
    public function getTablePrefix()
    {
        return $this->options['duckadmin_table_prefix'];
    }
    public function getSessionPrefix()
    {
        return $this->options['duckadmin_session_prefix'];
    }
    //// 以下是测试自留地 ////
    public function command_test()
    {
        // 我们还要一些特殊的方法，不在 web 下的操作的危险命令，如彻底抹杀某个员工等
        // 测试自留地
        // 我们测试一下
        var_dump("command_test");
    }
}
