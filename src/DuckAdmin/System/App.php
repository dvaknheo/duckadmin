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
    public $plugin_options = [
        'plugin_path_document' => 'res',
        'plugin_enable_readfile' =>true,
        'plugin_readfile_prefix' => '/res',
    ];
    public function __construct()
    {
        require_once __DIR__ .'/functions.php';
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
    /////////////////////
    public static function ResUrl($path)
    {
        return static::G()->_ResUrl($path);
    }
    public function _ResUrl($path)
    {
        // 如果设置了url 选项，那么从 url 选项里读取。
        // 否则从 默认的 /res/? 
        $path = ltrim($this->plugin_options['plugin_readfile_prefix'].'/'.$path,'/');
        return static::Url($path);
    }
}
