<?php declare(strict_types=1);
/**
 * DuckPhp
 * From this time, you never be alone~
 */

namespace DuckAdmin\System;

use DuckPhp\DuckPhp;
use DuckPhp\Component\AppPluginTrait;

/**
 * 入口类
 */
class App extends DuckPhp
{
    use AppPluginTrait;
    //@override
    public $options = [
        'is_debug' => true,        
        'use_setting_file' => true,
        'error_404' => '_sys/error_404',
        'error_500' => '_sys/error_500',
    ];
    /**
     * 这里是初始化基类，然后把自己当插件运行
     * @param array $options
     * @param type $plugin_options
     * @return type
     */
    public static function RunAsPlugin($options, $plugin_options = [])
    {
        $options['ext'][static::class] = $plugin_options;
        return DuckPhp::RunQuickly($options);
    }
    public function command_test()
    {
        // 我们还要一些特殊的方法，不在 web 下的操作的危险命令，如彻底抹杀某个员工等
        // 测试自留地
        // 我们测试一下
    }
    /**
     * 安装的方法
     */
    public function command_install()
    {
        // 安装命令
    }
    public function onPluginModeInit()
    {
        require_once __DIR__ .'/functions.php';
    }
    public static function ResUrl($path)
    {
        return static::G()->_ResUrl($path);
    }
    public function _ResUrl($path)
    {
        return static::URL($path);
    }
}
