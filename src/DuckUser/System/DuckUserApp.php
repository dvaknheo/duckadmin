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

class DuckUserApp extends DuckPhp
{
    //@override
    public $options = [
        'is_debug'=>true,
        
        'ext_options_from_config' => true,  //使用额外的选项
        'controller_class_postfix' => 'Controller', // 控制器后缀
        'controller_method_prefix' => 'action_', // 方法后缀
        
        //'exception_project' => ProjectException::class,
        //'exception_business' => BusinessException::class,
        //'exception_controller' => ControllerException::class,
        'exception_reporter' => ExceptionReporter::class,
        
        //'class_user' => UserApi::class,
        //'table_prefix' => '',   // 表前缀
        //'session_prefix' => '',  // Session 前缀
        
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
        $options = DbManager::_()->options;
        
        if (!empty($options['database']) || !empty($options['database_list'])){
            return;
        }
        
        $post = $this->options['database'] ?? null;
        if (empty($post)) {
            return;
        }
        $options['database']=$post;
        $options['reinit'] = true;
        
        DbManager::_( )->init($options, static::Root());
    }
    public static function DefaultService()
    {
        return ServiceApi::CallInPhase(static::class);
    }
    public static function DefaultAction()
    {
        return AcctionApi::CallInPhase(static::class);
    }
}
