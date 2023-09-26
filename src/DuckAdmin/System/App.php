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
class App extends DuckPhp
{
    //@override
    public $options = [
        'is_debug' => true,
        'error_404' => '_sys/error_404',
        'error_500' => '_sys/error_500',
        'controller_class_postfix' => 'Controller', // 控制器后缀
        'controller_resource_prefix' => 'res/',  // 资源文件前缀
        'ext_options_from_config' => true,
        'ext' =>[RouteHookResource::class => true],
    ];
    public function __construct()
    {
        $this->options['path'] = dirname(__DIR__).'/';
        parent::__construct();
    }
    public function install($options)
    {
        $this->installWithExtOptions($options);
        $this->switchDbManager(); // 切换数据库
        
        //复制资源文件
        $flag = preg_match('/^(https?:\/)?\//', $this->options['controller_resource_prefix'] ?? '');
        if($flag){ return; }
        
        $source = realpath(dirname(__DIR__).'/res/') .'/';
        $path = $this->options['controller_resource_prefix'];
        $path = (substr($path,0,1)==='/')? substr($path,1) : $this->options['controller_url_prefix'].$path;
        FileHelper::G()->copyDir($source,$_SERVER['DOCUMENT_ROOT'], $path,true,$info);
        //__debug_log($info);
    }
    public function onInit()
    {
        // 默认的路由没满足我们
        $this->switchRoute(ProjectRoute::class);
        //如果根应用没设置数据库，用自己的
        $this->switchDbManager();
        
        // 设置 Admin 为  admin 对象 ，让其他应用也能调
        $this->bumpAdmin(ActionApi::class);
        //本应用的 Admin 也切换过来
        static::Admin(ActionApi::G());
    }
    protected function switchDbManager()
    {
        $options = DbManager::G()->options;
        if (!empty($options['database']) || !empty($options['database_list'])){
            return;
        }
        
        $post = $this->options['database'] ?? null;
        if (empty($post)) {
            return;
        }
        $options['database']=$post;
        $options['force']=true;// DbManager 只会初始化一次，所以强制初始化。
        
        DbManager::G( )->init($options, static::Root());
    }
    protected function switchRoute($class)
    {
         //为了满足 webman admin 的路由 替换掉默认的路由，这里牺牲了点效率
        $class::G()->init(Route::G()->options, $this);
        $class::G()->pre_run_hook_list = Route::G()->pre_run_hook_list;
        $class::G()->post_run_hook_list = Route::G()->post_run_hook_list;
        Route::G($class::G());
    }
}
