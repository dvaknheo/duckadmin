<?php declare(strict_types=1);
/**
 * 这里我们做一下
 */
namespace DuckAdmin\System;

use DuckPhp\Component\DbManager;
use DuckPhp\Component\RouteHookResource;
use DuckPhp\Core\Route;
use DuckPhp\DuckPhp;

/**
 * 入口类
 */
class DuckAdminApp extends DuckPhp
{
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
        //static::Phase(static::class);
        $dsn = $this->options['database']['dsn'] ?? null;
        $a=explode(';',$dsn);
        $t =[];
        foreach($a as $v){ $c=explode('=',$v); @$t[$c[0]]=$c[1];}
        $dbname = $t['dbname'];
        exec("mysqldump $dbname >demo.sql");
        echo "dump to demo.sql done: ";
        echo (DATE(DATE_ATOM));
        echo "\n";
    }
    /**
     * dump demo sql
     */  
    public function command_install()
    {
        echo "TODO";
    }
    public function install($options, $parent_options = [])
    {
        //安装
        parent::install($options, $parent_options = []);
        
        //切换数据库配置
        $this->switchDbManager();
        
        // 资源文件修正
        RouteHookResource::_()->init($this->options, $this)->cloneResource();
    }
    public function onPrepare()
    {
        //默认的路由不符合我们这次的路由，换
        Route::_(ProjectRoute::_());
    }
    public function onInit()
    {
        
        //如果根应用没设置数据库，用自己的
        $this->switchDbManager();
    }
    protected function switchDbManager()
    {
        $options = DbManager::_()->options;
        if (!empty($options['database']) || !empty($options['database_list'])){
            return;
        }
        
        $data = $this->options['database'] ?? null;
        if (empty($data)) {
            return;
        }
        
        // DbManager 只会初始化一次，所以强制初始化。
        $options['force_new_init'] = true; 
        $options['database'] = $data;
        
        DbManager::_()->init($options, static::Root());
    }
    public function checkDatabase()
    {
        $this->switchDbManager();
        $options = DbManager::_()->options;
        if (!empty($options['database']) || !empty($options['database_list'])){
            return true;
        }
        return false;
    }
}
