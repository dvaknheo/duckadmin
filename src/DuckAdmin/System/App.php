<?php declare(strict_types=1);
/**
 * DuckPhp
 * From this time, you never be alone~
 */

namespace DuckAdmin\System
{

use DuckPhp\DuckPhp;

use DuckPhp\Ext\InstallableTrait;

/**
 * 入口类
 */
class App extends DuckPhp
{
    use InstallableTrait;
    //@override
    public $options = [
        'error_404' => '_sys/error_404',
        'error_500' => '_sys/error_500',
        
        'duckadmin_installed' => false,  // 检查安装
        'table_prefix' => '',   // 表前缀
        'session_prefix' => '',  // Session 前缀 
        'duckadmin_resource_prefix' => 'res/',  // 资源文件前缀 
    ];
    ///////
    /**
     * @override 覆盖初始化
     */
    public function onInit()
    {
        $this->options['controller_resource_prefix'] = $this->options['duckadmin_resource_prefix'];
        // 我们插件生效起来。没有这个资源则调用 res 目录的资源。
        // 如果是插件模式，则把插件也生效起来。
    }
    
    protected function onBeforeRun()
    {
        $this->checkInstall(); // checkInstall on InstallableTrait
    }

    ////////////// 命令行
    public function command_install()
    {
        echo "welcome to Use DuckAdmin installer  --force  to force install\n";
        $parameters =  static::Parameter();
        if(count($parameters)==1 || ($parameters['help'] ?? null)){
            // echo "--force  to force install ;";
            //return;
        }
        echo $this->install($parameters); // on InstallableTrait
        echo "Done \n";
    }
    /////////////////////

    //// 以下是测试自留地 ////
    public function command_test()
    {
        // 我们还要一些特殊的方法，不在 web 下的操作的危险命令，如彻底抹杀某个员工等
        // 测试自留地
        // 我们测试一下
        var_dump("command_test");
    }
}

}
namespace DuckAdmin
{
// 专属的一些函数

function __res($url)
{
    return \DuckAdmin\System\App::Res($url);
}
function __url($url)
{
    return \DuckAdmin\System\App::Url($url);
}

}