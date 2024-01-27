<?php declare(strict_types=1);
/**
 * DuckPHP
 * From this time, you never be alone~
 */
namespace SimpleBlog\System;

use DuckPhp\DuckPhp;
use SimpleBlog\Controller\ExceptionReporter;
use DuckPhp\Foundation\FastInstallerTrait;

class SimpleBlogApp extends DuckPhp
{
    use FastInstallerTrait;
    
    public $options = [
        'path' => __DIR__ . '/../',
        
        'exception_reporter' =>  ExceptionReporter::class,
        'exception_for_business'  => ProjectException::class,
        'exception_for_controller'  => ProjectException::class,
        
        'rewrite_map' => [
            '~article/(\d+)/?(\d+)?' => 'article?id=$1&page=$2',
        ],
    ];
}
