<?php declare(strict_types=1);
/**
 * DuckPhp
 * From this time, you never be alone~
 */

namespace DuckPear\System
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
        'controller_class_postfix' => 'Controller', // 控制器后缀
        'controller_resource_prefix' => 'res/',  // 资源文件前缀
        //////////////////
        'table_prefix' => '',   // 表前缀
        'session_prefix' => '',  // Session 前缀         
    ];
    ///////
    ////////////// 命令行
    public function command_install()
    {
        echo "welcome to use DuckPear installer  --force  to force install\n";
        $parameters =  static::Parameter();
        if(count($parameters)==1 || ($parameters['help'] ?? null)){
            // echo "--force  to force install ;";
            //return;
        }
        echo $this->install($parameters); // on InstallableTrait
        echo "Done \n";
    }
}
/////////////
}
namespace DuckPear
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