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
    @include_once(__DIR__.'/../../../DNMVCS/src/Core/AutoLoader.php');    // 这里用本地的最新版本 DuckPhp 方便测试可调。
    spl_autoload_register([DuckPhp\Core\AutoLoader::class ,'DuckPhpSystemAutoLoader']);
    spl_autoload_register($t);
}
////]]]]

$options = [
    'ext' => [
        // 后台管理系统
//*/
        \DuckAdmin\System\DuckAdminApp::class => [
            'controller_url_prefix' => 'app/admin/', // 访问路径
            'controller_resource_prefix' => 'res/',  // 资源文件前缀
        ],
        \DuckUser\System\DuckUserApp::class => [
            'controller_url_prefix' => 'user/', // 访问路径
            'controller_resource_prefix' => 'res/',  // 资源文件前缀
        ],
//*/
        \DuckUserManager\System\DuckUserManagerApp::class => [
            'controller_url_prefix' => 'app/admin/', // 访问路径
            'controller_resource_prefix' => 'res/',  // 资源文件前缀
        ],
//*/
        \SimpleBlog\System\SimpleBlogApp::class => [
            'controller_url_prefix' => 'app/admin/', // 访问路径
            'controller_resource_prefix' => 'res/',  // 资源文件前缀
        ],
//*/
    ]
];
$options['path'] = dirname(__DIR__).'/';
\DuckPhp\DuckPhp::InitAsContainer($options, function(){
    \DuckPhp\DuckPhp::Phase(DuckPhp\DuckPhp::class);
    \DuckPhp\Core\View::Show([], 'main');
})->run();