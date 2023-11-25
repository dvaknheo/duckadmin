<?php declare(strict_types=1);
/**
 * DuckPHP
 * From this time, you never be alone~
 */
namespace SimpleBlog\System;

use DuckPhp\DuckPhp;

class SimpleBlogApp extends DuckPhp
{
    //@override
    public $options = [       
        'error_404' =>'error-404',
        'error_500' => 'error-500',
        'rewrite_map' => [
            '~article/(\d+)/?(\d+)?' => 'article?id=$1&page=$2',
        ],
        'sql_dump_enable' => true,
    ];
    public function __construct()
    {
        $this->options['path'] = dirname(__DIR__).'/';
        parent::__construct();
    }
}
