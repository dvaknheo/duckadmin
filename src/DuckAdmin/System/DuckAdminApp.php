<?php declare(strict_types=1);
/**
 * 这里我们做一下
 */
namespace DuckAdmin\System;

use DuckPhp\Component\DbManager;
use DuckPhp\Component\RouteHookResource;
use DuckPhp\Core\Route;
use DuckPhp\DuckPhp;
use DuckAdmin\Controller\AdminSession;

/**
 * 入口类
 */
class DuckAdminApp extends DuckPhp
{

    //@override
    public $options = [
        'is_debug' =>true, //TODO 这里不继承根应用的，还得调试
        'controller_class_postfix' => 'Controller', // 控制器后缀
        'controller_resource_prefix' => 'res/',  // 资源文件前缀
        
        'ext_options_from_config' => true,  //使用额外的选项
        'ext' =>[RouteHookResource::class => true], //  资源配置
        
        
        'class_admin'=> AdminApi::class,
        
        'exception_project'=> ProjectException::class,
        'exception_business'=> ProjectException::class,
        'exception_controller'=> ProjectException::class,
    ];
    public function Action()
    {
        return ActionApi::InstanceInPhase(static::class);
    }
    public function Service()
    {
        return ServiceApi::InstanceInPhase(static::class);
    }
    public function __construct()
    {
        $this->options['path'] = dirname(__DIR__).'/';
        parent::__construct();
    }
    
    public function install($options)
    {
        //安装
        $this->installWithExtOptions($options);
        
        //切换数据库配置
        $this->switchDbManager();
        
        // 资源文件修正
        RouteHookResource::G()->init($this->options, $this)->replaceResource();
    }
    public function onPrepare()
    {
        //默认的路由不符合我们这次的路由，还过
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
        $options['force'] = true; 
        $options['database'] = $data;
        
        DbManager::_()->init($options, static::Root());
    }
}
