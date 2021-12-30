<?php declare(strict_types=1);
/**
 * DuckPhp
 * From this time, you never be alone~
 */

namespace DuckAdminDemo\System;

use DuckPhp\DuckPhp;
use DuckPhp\Component\Console;
use DuckPhp\Component\DbManager;
use DuckPhp\Component\DuckPhpCommand;
use DuckPhp\Ext\InstallableTrait;

class App extends DuckPhp
{
    use InstallableTrait;
    //@override
    public $options = [
        //'path_info_compact_enable' => false,        
    ];
    protected function onBeforeRun()
    {
        $this->checkInstall(); // checkInstall on InstallableTrait
    }
    public function __construct()
    {
        parent::__construct();
        
        $options =[];
        $options['ext'][\DuckAdmin\Api\DuckAdminPlugin::class]=[
            'plugin_url_prefix' => 'admin/',
            // 'duckadmin_resource_url_prefix' => '/res', // 资源前缀
            
            'duckadmin_installed' => true,
            'table_prefix' => '',
            'session_prefix' => '',
        ];
        $options['ext'][\DuckUser\Api\DuckUser::class]=[
            'plugin_url_prefix' => 'user/',
        ];
        $options['is_debug'] = true;
        //*/
        $options['ext'][\DuckMerchant\Api\DuckMerchant::class]=[
            'plugin_url_prefix' => 'merchant/',

        ];
        $this->options = array_merge($this->options, $options);
    }

    /**
     * 快速开始
     */
    public function command_run()
    {
        // 检查有没有设置，如果没有那么那么进入安装模式
        if (!$this->isInstalled()) {
            $setting = $this->doDatabaseSetting();
            $this->exportMySqlSetting($setting['host'], $setting['port'], $setting['dbname'], $setting['username'], $setting['password']);
            $this->install([]);
            
        }
        $this->runHttpServer();
        
    }
    protected function runHttpServer()
    {
        DuckPhpCommand::G()->command_run();
    }
    protected function doDatabaseSetting()
    {
        do {
            $desc = <<<EOT
Configing Database(Mysql):
Database host       [{host}]
Database port       [{port}]
Database dbname     [{dbname}]
Database username   [{username}]
Database password   [{password}]

done;

EOT;
            $options=[
                'host'=>'127.0.0.1',
                'port'=>'3306',
                'dbname'=>'t2',
                'username'=>'admin',
                'password'=>'123456',
            ]; 

            $setting = Console::G()->readLines($options,$desc);
            
            //然后我们读取一下配置，看是否正确
            $flag = $this->checkMySqlSetting($setting['host'], $setting['port'], $setting['dbname'], $setting['username'], $setting['password']);
            if(!$flag){
                echo "MySql Database config failed\n";
            }
        }while(!$flag);
        return $database_setting;
    }
    //////////////////////////////////////////
    public function checkMySqlSetting($host, $port, $dbname, $username, $password)
    {
        $database = [
            'dsn' => 'mysql:host='.$host.';port='.$port.';dbname='.$dbname.';charset=utf8mb4;',
            'username' => $username,
            'password' => $password,
            'driver_options' => [],
        ];
        DbManager::G()->init(['database'=>$database], $this);
        try{
            DbManager::Db()->fetch('select 1+1 as t');
            return true;
        }catch(\Exception $ex){
            return false;
        }
    }
    public function exportMySqlSetting($database_setting)
    {
        $database = [
            'dsn' => 'mysql:host='.$database_setting['host'].';port='.$database_setting['port'].';dbname='.$database_setting['dbname'].';charset=utf8mb4;',
            'username' => $database_setting['username'],
            'password' => $database_setting['password'],
            'driver_options' => [],
        ];
        file_put_contents($this->options['path'].'config/setting.php',"<"."?php\n return ". var_export(['database'=>$database],true).';');
    }
}
