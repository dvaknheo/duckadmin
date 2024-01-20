<?php declare(strict_types=1);
/**
 * DuckPHP
 * From this time, you never be alone~
 */
namespace DuckUser\System;

use DuckPhp\DuckPhp;
use DuckPhp\Component\DbManager;
use DuckUser\System\ActionApi;
use DuckUser\Controller\ExceptionReporter;

class DuckUserApp extends DuckPhp
{
    //@override
    public $options = [
        'is_debug' => true,
        'ext_options_file_enable' => true,
        'exception_reporter' => ExceptionReporter::class,
        
        'class_user' => User::class,
        //'table_prefix' => '',   // 表前缀
        //'session_prefix' => '',  // Session 前缀
        
        'home_url' => 'Home/index',
    ];
    public function install($options, $parent_options = [])
    {
        // 如果默认没设置数据库切换数据库
        
        $this->installWithExtOptions($options);
    }
    protected function onInit()
    {
        //
    }

}