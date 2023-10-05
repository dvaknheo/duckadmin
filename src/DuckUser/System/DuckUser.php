<?php declare(strict_types=1);
/**
 * DuckPHP
 * From this time, you never be alone~
 */
namespace DuckUser\System;

use DuckPhp\DuckPhp;
use DuckPhp\Component\DbManager;
use DuckUser\System\ActionApi;
use DuckUser\System\Session;

class DuckUser extends DuckPhp
{
    //@override
    public $options = [
        'is_debug'=>true,
        
        'ext_options_from_config' => true,  //使用额外的选项
        'controller_class_postfix' => 'Controller', // 控制器后缀
        
        'table_prefix' => '',   // 表前缀
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
        // 如果默认没设置数据库切换数据库
        
        $this->installWithExtOptions($options);
        $this->switchDbManager(); 
    }
    protected function onInit()
    {
        $this->switchDbManager(); 
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
        $options['force'] = true; // DbManager 只会初始化一次，所以强制初始化。
        
        DbManager::G( )->init($options, static::Root());
    }
}
