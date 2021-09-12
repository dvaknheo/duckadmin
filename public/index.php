<?php declare(strict_types=1);
/**
 * DuckPhp
 * From this time, you never be alone~
 */
 
@include_once(__DIR__.'/../../DNMVCS/autoload.php');    // 这里用本地的最新版本 DuckPhp 方便测试可调。

require_once(__DIR__.'/../vendor/autoload.php');

//////////////////////////////////////////////////
$options = [
    'ext' => [
    ],
];
///*

$options['ext'][DuckAdmin\Api\DuckAdminPlugin::class]=[
    'plugin_url_prefix' => 'admin/',
    // 'duckadmin_resource_url_prefix' => '/res', // 资源前缀
    
    'duckadmin_installed' => true,
    'table_prefix' => '',
    'session_prefix' => '',
];
$options['ext'][DuckUser\Api\DuckUser::class]=[
    'plugin_url_prefix' => 'user/',
];
//*/
$options['ext'][DuckMerchant\Api\DuckMerchant::class]=[
    'plugin_url_prefix' => 'merchant/',

];

if (!class_exists(\DuckAdminDemo\System\App::class)) {
    \DuckPhp\DuckPhp::assignPathNamespace(__DIR__ . '/../app', "DuckAdminDemo\\"); 
    \DuckPhp\DuckPhp::runAutoLoader();
}
DuckAdminDemo\System\App::RunQuickly($options);
