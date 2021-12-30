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
        'controller_base_class' => ProjectController::class,  // 我们固定住控制器基类
        'controller_resource_prefix' => 'res/',  // 资源文件前缀
        //////////////////
        
        'table_prefix' => '',   // 表前缀
        'session_prefix' => '',  // Session 前缀 
        'duckadmin_installed' => false,  // 检查安装 InstallableTrait 用到 InstallableTrait 这里要调整回去
        
    ];
    ///////
    /**
     * @override 覆盖初始化
     */
    public function init(array $options, ?object $context = NULL)
    {
        parent::init($options, $context);
        
        //$this->checkInstall(); // checkInstall on InstallableTrait
        
        return $this;
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