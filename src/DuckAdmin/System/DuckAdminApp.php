<?php declare(strict_types=1);
/**
 * 这里我们做一下
 */
namespace DuckAdmin\System;

use DuckPhp\Component\DbManager;
use DuckPhp\Component\RouteHookResource;
use DuckPhp\Core\Route;
use DuckPhp\DuckPhp;
use DuckPhp\Ext\InstallerTrait;
use DuckPhp\Component\SqlDumper;

/**
 * 入口类
 */
class DuckAdminApp extends DuckPhp
{
    use InstallerTrait;
    //@override
    public $options = [
        'path' => __DIR__ . '/../',
        'controller_method_prefix' => '', // 控制器后缀
        'controller_resource_prefix' => 'res/',  // 资源文件前缀
        'ext_options_file_enable' => true,  //使用额外的选项
        
        'class_admin'=> Admin::class,
    ];
    /**
     * dump demo sql
     */   
    public function command_dumpsql()
    {
        $dsn = static::Root()->options['database_list'][0]['dsn'] ?? null;
        $a=explode(';',$dsn);
        $t =[];
        foreach($a as $v){ $c=explode('=',$v); @$t[$c[0]]=$c[1];}
        $dbname = $t['dbname'];
        exec("mysqldump $dbname >demo.sql");
        echo "dump to demo.sql done: ";
        echo (DATE(DATE_ATOM));
        echo "\n";
    }
    public function command_dump()
    {
        SqlDumper::_()->init($this->options,$this)->run();
        var_dump(DATE(DATE_ATOM));
    }

    protected function onPrepare()
    {
        //默认的路由不符合我们这次的路由，换
        Route::_(ProjectRoute::_());
    }
}
