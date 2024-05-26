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
        
        'exception_reporter' =>  ExceptionReporter::class,
        'exception_for_business'  => ProjectException::class,
        'exception_for_controller'  => ProjectException::class,
        'rewrite_map' => [
            '~article/(\d+)/?(\d+)?' => 'article?id=$1&page=$2',
        ],
        
        'need_install'=>true,
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
