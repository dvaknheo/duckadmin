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
        'is_debug' => true,        
        'use_setting_file' => true,
        'error_404' => '_sys/error_404',
        'error_500' => '_sys/error_500',
        
        'duckadmin_check_installed' => true,  // 检查安装
        'duckadmin_table_prefix' => '',   // 表前缀
        'duckadmin_session_prefix' => '',  // Session 前缀 
    ];
    public function __construct()
    {
        require_once __DIR__ .'/functions.php';
    }
    protected function onBeforeRun()
    {
        $this->checkInstall();
    }
    protected function checkInstall()
    {
        if (!$this->options['duckadmin_check_installed']){
            return;
        }
        $has_database = (static::Setting('database') ||  static::Setting('database_list')) ? true : false;
        //Installer::CheckInstall([],$this , $has_database);
    }
    //////////////////////
    public function install($parameters)
    {
        $force = $parameters['force']?? false;
        
        $options = [
            'force' => $force,
           
        ];
        
        $options['path'] = $this->getPath();
        $options['sql_dump_inlucde_tables'] = [ 'Users']; // TODO  从 Model 类里来
        $options['sql_dump_install_new_prefix'] = $this->options[ 'duckadmin_table_prefix'];
        
        return Installer::G()->init($options,$this)->run();
    }
    
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
    public function getPath()
    {
        return $this->options['path'];
    }
    public function getTablePrefix()
    {
        return $this->options['duckadmin_table_prefix'];
    }
    public function getSessionPrefix()
    {
        return $this->options['duckadmin_session_prefix'];
    }
    
    public static function ResUrl($path)
    {
        return static::G()->_ResUrl($path);
    }
    public function _ResUrl($path)
    {
        // 如果设置了url 选项，那么从 url 选项里读取。
        // 否则从 默认的 /res/? 
        $path = ltrim($this->plugin_options['plugin_readfile_prefix'].'/'.$path,'/');
        return static::Url($path);
    }
    ///////////////////
    public function command_test()
    {
        // 我们还要一些特殊的方法，不在 web 下的操作的危险命令，如彻底抹杀某个员工等
        // 测试自留地
        // 我们测试一下
    }
    
    
}
