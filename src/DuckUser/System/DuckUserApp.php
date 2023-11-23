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
        'is_debug' => true,
        'exception_reporter' => ExceptionReporter::class,
        'ext_options_file_enable' => true,
        
        //'table_prefix' => '',   // 表前缀
        //'session_prefix' => '',  // Session 前缀
        
        'home_url' => 'Home/index',
    ];
    public function install($options, $parent_options = [])
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
        
        //$this->_UserSystem(MyUserSystem::CallInPhase(static::class));
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
