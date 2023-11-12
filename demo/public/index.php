<?php declare(strict_types=1);
/**
 * DuckPhp
 * From this time, you never be alone~
 */
 
////[[[[
// 这里用本地的最新版本 DuckPhp 方便测试可调。
if(is_file(__DIR__.'/../../../DNMVCS/autoload.php')){
    @include_once(__DIR__.'/../../../DNMVCS/src/Core/AutoLoader.php');    // 这里用本地的最新版本 DuckPhp 方便测试可调。
    spl_autoload_register([DuckPhp\Core\AutoLoader::class ,'DuckPhpSystemAutoLoader']);
    spl_autoload_register($t);
}
require_once(__DIR__.'/../../vendor/autoload.php');
////]]]]
////////////////


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
\DuckPhp\DuckPhpAllInOne::InitAsContainer($options)->thenRunAsContainer( false, function(){
    \DuckPhp\DuckPhpAllInOne::Phase(DuckPhp\DuckPhp::class);
    \DuckPhp\DuckPhpAllInOne::Show([], 'main');
});