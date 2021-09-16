<?php declare(strict_types=1);
/**
 * DuckPhp
 * From this time, you never be alone~
 */
 
@include_once(__DIR__.'/../../DNMVCS/autoload.php');    // 这里用本地的最新版本 DuckPhp 方便测试可调。

require_once(__DIR__.'/../vendor/autoload.php');

//////////////////////////////////////////////////
$options = [];

$options['ext'][DuckAdmin\Api\DuckAdminPlugin::class]=[
    'plugin_url_prefix' => 'admin/',
    // 'duckadmin_resource_url_prefix' => '/res', // 资源前缀
    
    'duck_admin_installed' => false,
    //'table_prefix' => '',
    //'session_prefix' => '',
];

DuckPhp\DuckPhp::RunQuickly($options);
