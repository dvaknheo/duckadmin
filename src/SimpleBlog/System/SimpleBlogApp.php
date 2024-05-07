<?php declare(strict_types=1);
/**
 * DuckPHP
 * From this time, you never be alone~
 */
namespace SimpleBlog\System;

use DuckPhp\Core\EventManager;
use DuckPhp\Core\Console;
use DuckPhp\DuckPhp;
use DuckPhp\FastInstaller\FastInstaller;
use SimpleBlog\Controller\ExceptionReporter;

class SimpleBlogApp extends DuckPhp
{
    public $options = [

        'path' => __DIR__ . '/../',
        'exception_reporter' =>  ExceptionReporter::class,
        'exception_for_business'  => ProjectException::class,
        'exception_for_controller'  => ProjectException::class,
        'rewrite_map' => [
            '~article/(\d+)/?(\d+)?' => 'article?id=$1&page=$2',
        ],
        'cli_command_classes' => [FastInstaller::class],
        
        'database_driver'=>'sqlite',
        
        'install_input_desc' => <<<EOT
欢迎使用SimpleBlog
EOT
        ,
    ];
    public function onInit()
    {
        //
    }
    
}
