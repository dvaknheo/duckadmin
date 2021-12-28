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

    /**
     * 快速开始
     */
    public function command_run()
    {
        // 检查有没有设置，如果没有那么那么进入安装模式
        if (!$this->isInstalled()) {
            $database_setting = $this->doDatabaseSetting();
            $database = [
                'dsn' => 'mysql:host='.$database_setting['host'].';port='.$database_setting['port'].';dbname='.$database_setting['dbname'].';charset=utf8mb4;',
                'username' => $database_setting['username'],
                'password' => $database_setting['password'],
                'driver_options' => [],
            ];
            file_put_contents($this->options['path'].'config/setting.php',"<"."?php\n return ". var_export(['database'=>$database],true).';');
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

            $database_setting = Console::G()->readLines($options,$desc);
            
            //然后我们读取一下配置，看是否正确
            $flag = $this->checkDatabaseSetting($database_setting);
            if(!$flag){
                echo "database config failed\n";
            }
        }while(!$flag);
        return $database_setting;
    }
    protected function checkDatabaseSetting($database_setting)
    {
        $database = [
            'dsn' => 'mysql:host='.$database_setting['host'].';port='.$database_setting['port'].';dbname='.$database_setting['dbname'].';charset=utf8mb4;',
            'username' => $database_setting['username'],
            'password' => $database_setting['password'],
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

}
