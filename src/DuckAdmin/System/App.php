<?php declare(strict_types=1);
/**
 * ，这里我们做一下
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
        'controller_base_class' => ProjectController::class,  // 我们固定住控制器基类
        'controller_resource_prefix' => 'res/',  // 资源文件前缀
        //////////////////
    ];
    ///////
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
}
/////////////
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