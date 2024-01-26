<?php
require_once(__DIR__.'/../vendor/autoload.php');

////[[[[
// 这里用本地的最新版本 DuckPhp 方便测试可调。
if(is_file(__DIR__.'/../../DNMVCS/autoload.php')){
    $funcs = spl_autoload_functions();
    $t =$funcs[0];
    spl_autoload_unregister($t);
    @include_once(__DIR__.'/../../DNMVCS/autoload.php');    // 这里用本地的最新版本 DuckPhp 方便测试可调。
    spl_autoload_register([DuckPhp\Core\AutoLoader::class ,'DuckPhpSystemAutoLoader']);
    spl_autoload_register($t);
}
////]]]]
require_once(__DIR__. '/DemoApp.php');
require_once(__DIR__. '/FixedWorkermanHttpd.php');

$options=[
    'cli_mode'=>true,
];
DuckPhp\HttpServer\HttpServer::_(FixedWorkermanHttpd::_());
DemoApp::RunQuickly($options);
