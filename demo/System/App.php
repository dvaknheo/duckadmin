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
use DuckPhp\HttpServer\HttpServer;

use WorkermanHttpd\WorkermanHttpd;

class App extends DuckPhp
{
    use InstallableTrait;
    //@override
    public $options = [
        'use_env_file'=>true,
        // 'path_info_compact_enable' => false,  //如果你的服务器不做 path_info 用这个
        'error_404' => '_sys/error_404',
        'error_500' => '_sys/error_500',
    ];
    
    public function onBeforeRun()
    {
        //我们检查安装
    }

    public function __construct()
    {
        parent::__construct();
        $ext = [
            // 后台管理系统
            \DuckAdmin\Api\DuckAdminPlugin::class => [
                'plugin_url_prefix' => 'admin/', // 访问路径
            ],
            // 前台用户系统
            \DuckUser\Api\DuckUserPlugin::class => [
                'plugin_url_prefix' => 'user/', // 访问路径
            ],
        ];
        $this->options['ext']=array_merge($this->options['ext'],$ext);
   }


    public function command_run()
    {
        // 检查有没有设置，如果没有那么那么进入安装模式
        if (false && !$this->isInstalled()) {
            $setting = $this->doDatabaseSetting();
            $this->exportMySqlSetting($setting['host'], $setting['port'], $setting['dbname'], $setting['username'], $setting['password']);
            $this->install([]);
        }
        
        HttpServer::G(WorkermanHttpd::G()); //我们使用 WorkermanHttpd 作为容器启动
        
        DuckPhpCommand::G()->command_run();
        
    }

    ////////////////// 安装系统， 这后面的我们以后要整合起来，变成公用
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
                'dbname'=>'duckphp',
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
        //还是写到 env 文件吧
    }
}
