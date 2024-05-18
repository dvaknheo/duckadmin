<?php
require_once(__DIR__.'/../vendor/autoload.php');

////[[[[
// 这里用本地的最新版本 DuckPhp 方便测试可调。 // 应该放到 vendor 里。
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
require_once(__DIR__. '/DemoAppWithDev.php');
require_once(__DIR__. '/FixedWorkermanHttpd.php');

$options=[
];

//WorkermanHttpd::_()->options['on_init']=
\DuckPhp\HttpServer\HttpServer::_(FixedWorkermanHttpd::_());
// 为什么 cli.php 就能 run , cli_dev.php 就不能run ?
\Demo\DemoAppWithDev::RunQuickly($options);
