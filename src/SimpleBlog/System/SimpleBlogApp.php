<?php declare(strict_types=1);
/**
 * DuckPHP
 * From this time, you never be alone~
 */
namespace SimpleBlog\System;

use DuckPhp\DuckPhp;
use SimpleBlog\Controller\ExceptionReporter;

class SimpleBlogApp extends DuckPhp
{
    public $options = [
        'path' => __DIR__ . '/../',
        'namespace' => 'SimpleBlog',
        //'is_maintain'=>true,
        
        'exception_reporter' =>  ExceptionReporter::class,
        'exception_for_business'  => ProjectException::class,
        'exception_for_controller'  => ProjectException::class,
        'rewrite_map' => [
            '~article/(\d+)/?(\d+)?' => 'article?id=$1&page=$2',
        ],
        
        //'error_maintain'=>'_sys/error_maintain',
        //'error_need_install'=>'_sys/error_need_install',
        
        'need_install'=>true,
        'local_database'=>true,
        'database_driver'=>'sqlite',
        
        'cli_command_with_fast_installer' => true,
        'install_input_desc' => <<<EOT
welcome to use SimpleBlog
EOT
        ,
    ];
    public function onInit()
    {
        //
    }
    public function onInstall()
    {
    }
    public function onInstalled()
    {
    }
}
