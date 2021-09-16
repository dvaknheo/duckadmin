<?php declare(strict_types=1);
/**
 * DuckPhp
 * From this time, you never be alone~
 */
 
require_once(__DIR__.'/../vendor/autoload.php');

////[[[[
// 这里用本地的最新版本 DuckPhp 方便测试可调。
if(is_file(__DIR__.'/../../DNMVCS/autoload.php')){
    $funcs = spl_autoload_functions();
    $t =$funcs[0];
    spl_autoload_unregister($t);
    @include_once(__DIR__.'/../../DNMVCS/src/Core/AutoLoader.php');    // 这里用本地的最新版本 DuckPhp 方便测试可调。
    spl_autoload_register([DuckPhp\Core\AutoLoader::class ,'DuckPhpSystemAutoLoader']);
    spl_autoload_register($t);
}
////]]]]

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
$options['is_debug'] = true;
//*/
$options['ext'][DuckMerchant\Api\DuckMerchant::class]=[
    'plugin_url_prefix' => 'merchant/',

];

if (!class_exists(\DuckAdminDemo\System\App::class)) {
    \DuckPhp\DuckPhp::assignPathNamespace(__DIR__ . '/../app', "DuckAdminDemo\\"); 
    \DuckPhp\DuckPhp::runAutoLoader();
}
DuckAdminDemo\System\App::RunQuickly($options);
