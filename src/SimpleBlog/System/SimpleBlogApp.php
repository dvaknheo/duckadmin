<?php declare(strict_types=1);
/**
 * DuckPHP
 * From this time, you never be alone~
 */
namespace SimpleBlog\System;

use DuckPhp\DuckPhp;
use DuckPhp\Foundation\CommonCommandTrait;
use DuckPhp\Foundation\FastInstallerTrait;
use SimpleBlog\Controller\ExceptionReporter;

class SimpleBlogApp extends DuckPhp
{
    use CommonCommandTrait;
    use FastInstallerTrait;
    
    public $options = [
        'path' => __DIR__ . '/../',
        'cli_command_class' => Command::class,
        'exception_reporter' =>  ExceptionReporter::class,
        'exception_for_business'  => ProjectException::class,
        'exception_for_controller'  => ProjectException::class,
        
        'rewrite_map' => [
            '~article/(\d+)/?(\d+)?' => 'article?id=$1&page=$2',
        ],
        'database_driver'=>'sqlite',
    ];
}
