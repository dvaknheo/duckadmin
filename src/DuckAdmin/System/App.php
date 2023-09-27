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
class App extends DuckPhp
{
    public static function _($object = null)
    {
        return static::G($object);
    }
    //@override
    public $options = [
        //'is_debug' =>true, //TODO 这里不继承根应用的，还得调试
        'controller_class_postfix' => 'Controller', // 控制器后缀
        'controller_resource_prefix' => 'res/',  // 资源文件前缀
        
        'ext_options_from_config' => true,
        'ext' =>[RouteHookResource::class => true],
        
        
        'class_admin'=> ActionApi::class,
        
        'exception_project'=> ProjectException::class,
        'exception_business'=> ProjectException::class,
        'exception_controller'=> ProjectException::class,
    ];
    public function Action()
    {
        ActionApi::$AppClass = static::class;
        return ActionApi::G();
    }
    public function Service()
    {
        ServiceApi::$AppClass = static::class;
        return ServiceApi::G();
    }
    public function __construct()
    {
        $this->options['path'] = dirname(__DIR__).'/';
        parent::__construct();
    }
    
    public function install($options)
    {
        $this->installWithExtOptions($options);
        $this->switchDbManager(); // 切换数据库
        
        // 资源文件修正
        RouteHookResource::G()->init($this->options, $this)->replaceResource();
    }
    public function onPrepare()
    {
        //TODO 这里出错会变空白，没进入错误处理程序
        Route::G(ProjectRoute::G());
    }
    public function onInit()
    {
        //如果根应用没设置数据库，用自己的
        $this->switchDbManager();
    }
    protected function switchDbManager()
    {
        $options = DbManager::G()->options;
        if (!empty($options['database']) || !empty($options['database_list'])){
            return;
        }
        
        $data = $this->options['database'] ?? null;
        if (empty($data)) {
            return;
        }
        $options['database'] = $data;
        $options['force'] = true; // DbManager 只会初始化一次，所以强制初始化。
        
        DbManager::G( )->init($options, static::Root());
    }
}
