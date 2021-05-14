<?php declare(strict_types=1);
/**
 * DuckPhp
 * From this time, you never be alone~
 */

namespace DuckAdmin\App;

use DuckPhp\DuckPhp;
use DuckPhp\Component\AppPluginTrait;
class App extends DuckPhp
{
    use AppPluginTrait;
    //@override
    public $options = [
        'is_debug' => true,        
        'use_setting_file' => true,
        'error_404' => '_sys/error_404',
        'error_500' => '_sys/error_500',
        
        //'path_info_compact_enable' => false,        
    ];
    public function runAsPlugin()
    {
        //这里是初始化基类，然后把自己当插件运行
    }
    public function command_test()
    {
        // 测试自留地
        // 我们测试一下
    }
}
