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
function worker()
{
    $options = [
        'namespace_controller' => '\\',
        'controller_class_postfix' => 'Controller',
        'ext' => [
            // 后台管理系统
            \DuckAdmin\System\App::class => [
                'controller_url_prefix' => 'app/admin/', // 访问路径
                'controller_resource_prefix' => 'http://admin.demo.local/res/',  // 资源文件前缀，这里可以修改一下
            ],
        ],
        'cli_enable'=>false,
    ];
    $options['path'] = __DIR__.'/';

    DuckPhp\DuckPhp::RunQuickly($options, function(){
        DuckPhp\DuckPhp::system_wrapper_replace(\WorkermanHttpd\WorkermanHttpd::system_wrapper_get_providers());
    });


        return true;
}
$options =[
    'http_handler' => 'worker',
    'http_handler_root' => '',
    'with_http_handler_file' => true,
];
\WorkermanHttpd\WorkermanHttpd::RunQuickly($options); // 我们需要热修一下，以及除了 宏改变导致的问题
class MainController
{
    public function index()
    {
        DuckPhp\DuckPhp::Show(get_defined_vars(), 'main');
    }
}