<?php declare(strict_types=1);
/**
 * DuckPhp
 * From this time, you never be alone~
 */

require_once(__DIR__.'/../vendor/autoload.php');    //@DUCKPHP_HEADFILE

$options = [
    'is_debug'=>true,
    'ext'=>[
        \DuckAdmin\App\App::class =>[
            //'xxxxxxxxxxxxxxx' => true,
            'plugin_url_prefix'=>'/admin',
        ],
    ],
];

\DuckPhp\DuckPhp::RunQuickly($options,function(){
        \DuckPhp\DuckPhp::G()->add404RouteHook(function () {
            $path_info = \DuckPhp\DuckPhp::getPathInfo();
            $path_info = ltrim($path_info, '/');
            $path_info = empty($path_info)?'index':$path_info;
                
            $post_prefix = !empty($_POST)?'do_':'';
            $callback = "action_{$post_prefix}{$path_info}";
                
            if (is_callable($callback)) {
                ($callback)();
                return true;
            }
            action_index();
            return true;
        });
});
function action_index()
{
    echo '正在使用插件模式';
}
