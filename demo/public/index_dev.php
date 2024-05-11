<?php declare(strict_types=1);
/**
 * DuckPhp
 * From this time, you never be alone~
 */

require_once(__DIR__.'/../../vendor/autoload.php');

////[[[[
// 这里用本地的最新版本 DuckPhp 方便测试可调。
if(is_file(__DIR__.'/../../../DNMVCS/autoload.php')){
    $funcs = spl_autoload_functions();
    $t =$funcs[0];
    spl_autoload_unregister($t);
    @include_once(__DIR__.'/../../../DNMVCS/autoload.php');    // 这里用本地的最新版本 DuckPhp 方便测试可调。
    spl_autoload_register([DuckPhp\Core\AutoLoader::class ,'DuckPhpSystemAutoLoader']);
    spl_autoload_register($t);
}
////]]]]

require_once(__DIR__. '/../DemoApp.php');
require_once(__DIR__. '/../DemoAppWithDev.php');
require_once(__DIR__. '/../MyCoverage.php');
require_once(__DIR__. '/../MyCoverageBridge.php');
$options=[
    // ...
];
\Demo\DemoAppWithDev::RunQuickly($options);
