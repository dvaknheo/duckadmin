<?php declare(strict_types=1);
/**
 * DuckPHP
 * From this time, you never be alone~
 */
namespace DuckAdmin\SimpleBlog\System;

use DuckPhp\DuckPhp;
use DuckPhp\Ext\RouteHookWebInstaller;
class SimpleBlogApp extends DuckPhp
{
    public $options = [
        'path' => __DIR__ . '/../',
        'namespace' => 'SimpleBlog',
        'name'  => 'SimpleBlog',
        'data_file_enable' => true,

        'ext' => [
            RouteHookWebInstaller::class => [
                'web_installer_use_redis' => false,
                'web_installer_use_database' => true,
                'web_installer_database_drivers' => ['sqlite' => true],
                //'web_installer_view' => 'install',
            ],
        ],
        
        'rewrite_map' => [
            '~article/(\d+)/?(\d+)?' => 'article?id=$1&page=$2',
        ],
        
    ];
}
