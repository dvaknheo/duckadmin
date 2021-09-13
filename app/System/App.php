<?php declare(strict_types=1);
/**
 * DuckPhp
 * From this time, you never be alone~
 */

namespace DuckAdminDemo\System;

use DuckPhp\DuckPhp;

class App extends DuckPhp
{
    //@override
    public $options = [
        //'path_info_compact_enable' => false,        
    ];
    public function onBeforeRun()
    {
        //$this->
    }
    protected function checkInstall()
    {
        //
    }
    /**
     * 快速开始
    */
    public function command_go()
    {
        // 检查有没有设置，如果没有那么那么进入安装模式
        //if(!(static::Setting('database') || static::Setting('database_list')){
            //$this->doDatabaseSetting();
        //}
        
        
    }
    protected function runHttpServer()
    {
        $options = static::Parameters();
        $options['path'] = $this->options['path'];
        if (!empty($options['http_server'])) {
            $class = str_replace('/', '\\', $options['http_server']);
            HttpServer::G($class::G());
        }
        HttpServer::RunQuickly($options);
    }
    protected function doDatabaseSetting()
    {
        $desc = <<<EOT
Configure Database(Mysql)

database host[{host}]
database port[{port}]
database dbname[{dbname}]
database username[{username}]
database password[{password}]

done;

EOT;
        $options=[
            'host'=>'127.0.0.1',
            'port'=>'3306',
        ];        
        $database_setting = ConsoleParent::G()->readLines($options,$desc);
        $setting = static::Setting();
        
/*
    'database_list' => [
        [
        'dsn' => 'mysql:host=127.0.0.1;port=3306;dbname=duckadmin;charset=utf8mb4;',
        'username' => 'admin',
        'password' => '123456',
        'driver_options' => [],
        ],
    ],
//*/
    }
}
