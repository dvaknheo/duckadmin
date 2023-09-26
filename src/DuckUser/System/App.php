<?php declare(strict_types=1);
/**
 * DuckPHP
 * From this time, you never be alone~
 */
namespace DuckUser\System;

use DuckPhp\DuckPhp;
use DuckUser\System\ActionApi;
use DuckUser\System\Session;

class App extends DuckPhp
{
    //@override
    public $options = [
		'controller_class_postfix' => 'Controller',
        
        'table_prefix' => 'duckuser_',   // 表前缀
        'session_prefix' => '',  // Session 前缀
        
        'class_session' => Session::class,
        'class_user' => ActionApi::class,
        'exception_project' => ProjectException::class,
        'exception_business' => ProjectException::class,
        'exception_controller' => ProjectException::class,
        
        'home_url' => 'Home/index',
    ];
    public function install($options)
    {
        $this->installWithExtOptions($options);
        $this->switchDbManager(); // 如果默认没设置数据库切换数据库
        
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
}
