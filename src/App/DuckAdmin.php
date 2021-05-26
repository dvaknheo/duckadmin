<?php declare(strict_types=1);
/**
 * DuckPhp
 * From this time, you never be alone~
 */

namespace DuckAdmin\App;

use DuckPhp\DuckPhp;
use DuckPhp\Component\AppPluginTrait;
use DuckAdmin\Service\BaseService;
use DuckAdmin\Controller\BaseController;
class DuckAdmin extends DuckPhp
{
    use AppPluginTrait;
    //@override
    public $options = [
        'is_debug' => true,        
        'use_setting_file' => true,
        'error_404' => '_sys/error_404',
        'error_500' => '_sys/error_500',
    ];
    public static function RunAsPlugin($options, $plugin_options = [])
    {
        $options['ext'][static::class] = $plugin_options;
        return DuckPhp::RunQuickly($options);
        //这里是初始化基类，然后把自己当插件运行
    }
    public function command_test()
    {
        // 我们还要一些特殊的方法，不在 web 下的操作的危险命令，如彻底抹杀某个员工等
        // 测试自留地
        // 我们测试一下
    }
    /**
     * 
     * @return BaseService
     */
    public static function Service() : BaseService
    {
        return BaseSerivce::G();
        
    }
    /**
     * 
     * @return BaseService
     */
    public static function XService()
    {
        return BaseService::class;
    }
    public static function Controller()
    {
        return; //BaseControllerProxy::G();
    }
    public function foo()
    {

    }
}
