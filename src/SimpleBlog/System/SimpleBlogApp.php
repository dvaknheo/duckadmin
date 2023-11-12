<?php declare(strict_types=1);
/**
 * DuckPHP
 * From this time, you never be alone~
 */
namespace SimpleBlog\System;

use DuckPhp\DuckPhp;

class App extends DuckPhp
{
    //@override
    public $options = [       
        'error_404' =>'error-404',
        'error_500' => 'error-500',
        
        'rewrite_map' => [
            '~article/(\d+)/?(\d+)?' => 'article?id=$1&page=$2',
        ],
        'controller_method_prefix' => '',
        'table_prefix' => '',
        'session_prefix' => '',
    ];
}
