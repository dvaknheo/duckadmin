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
    //@override
    public $options = [
        'path' => __DIR__ . '/../',
        
        'exception_reporter' =>  ExceptionReporter:class,
        //'exception_default_class'  => ProjectException::class,
        
        'rewrite_map' => [
            '~article/(\d+)/?(\d+)?' => 'article?id=$1&page=$2',
        ],
        'sql_dump_enable' => true,
    ];
}
