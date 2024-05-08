<?php declare(strict_types=1);
/**
 * DuckPhp
 * From this time, you never be alone~
 */
use SebastianBergmann\CodeCoverage\CodeCoverage;
use SebastianBergmann\CodeCoverage\Report\Html\Facade as ReportOfHtmlOfFacade;
use SebastianBergmann\CodeCoverage\Report\PHP as ReportOfPHP;

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

$options=[
    // 'cli_mode'=>true,
    // ...
];

//$path = '';
//$name = '';
//$output_file ='';

//$coverage = new CodeCoverage();
//$coverage->filter()->addDirectoryToWhitelist($path);
//$coverage->start($name);

//$coverage->stop();
//(new ReportOfPHP)->process($coverage, $output_file);

DemoApp::RunQuickly($options);

