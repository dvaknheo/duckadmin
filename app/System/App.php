<?php declare(strict_types=1);
/**
 * DuckPhp
 * From this time, you never be alone~
 */

namespace DuckAdminDemo\System;

use DuckPhp\DuckPhp;
use DuckPhp\Component\Console;
use DuckPhp\HttpServer\HttpServer;


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
    public function command_run()
    {
        // 检查有没有设置，如果没有那么那么进入安装模式
        if (true || !(static::Setting('database')) && !(static::Setting('database_list'))) {
            $this->doDatabaseSetting();
        }
        $this->runHttpServer();
        
    }
    protected function runHttpServer()
    {
        $options = static::Parameter();
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
配置数据库 Configure Database(Mysql):
Welcome
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
        ];        
        $database_setting = Console::G()->readLines($options,$desc);
        //然后我们读取一下配置，看是否正确
    }
}
