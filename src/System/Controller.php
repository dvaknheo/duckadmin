<?php declare(strict_types=1);
/**
 * DuckPhp
 * From this time, you never be alone~
 */

namespace DuckAdmin\System;

use DuckPhp\Helper\ControllerHelperTrait;
use DuckPhp\SingletonEx\SingletonExTrait;

use DuckAdmin\Business\SessionBusiness;
use DuckAdmin\Business\AdminBusiness;


/**
 * 这是充当 Helper 助手的 控制器基类
 * 这里要注意的是，控制器的公开动态方法都会当成 web 动作，所以尽量避免公开动态方法
 * 第三方的东西在这里写
 */
class Controller
{
    use SingletonExTrait;
    use ControllerHelperTrait;
    
    public function __construct()
    {
        $this->initialize();
    }
    /*
    */
    protected function initialize()
    {
        // 入口类
        static::assignExceptionHandler(\Exception::class, function(){
            // 这里应该调整成可调的
            static::ExitRouteTo('login?r=' . static::getPathInfo());
        });
        
        $admin = SessionBusiness::G()->getCurrentAdmin();
        $path_info = static::getPathInfo();
        $flag = AdminBusiness::G()->checkPermission($admin,$path_info);
        
        if(!$flag){
            static::Exit404();
            return;
        }
        // 初始化View
        $this->initViewData();
    }
    // 这里是视图相关的
    protected function initViewData()
    {
        // 这两个重复调用，性能可以忽略不记。
        $admin = SessionBusiness::G()->getCurrentAdmin();
        $path_info = static::getPathInfo();
        
        $menu = AdminBusiness::G()->getMenu($admin['id'],$path_info);
        
        static::assignViewData('menu', $menu);
        static::assignViewData('admin', $admin);
        static::setViewHeadFoot('header','footer');
        // 页眉页脚可能你的类需要额外处理。
    }
    
    public static function CheckLocalController($self,$static)
    {
        // 这个方法可能要给回 DuckPhp
        if ($self === $static) {
            if ($self === App::Route()->getRouteCallingClass()) {
                //禁止直接访问
                static::Exit404();
            }
            // 作为助手调用。
            return true;
        }
        return false;
    }
    //////// 提供给第三方用的静态方法 ////////
    public static function CheckPermission()
    {
        return static::G()->doCheckPermission();
        // static::G() 是为了可替换。
        // 这就导致了 限定 Controller::G(MyController::G()) 的 MyController 必须是子类。
        // 不必奢望不继承了。
    }
    //////// 验证码这段，要到第三个类里来处理吧 ////////
    public static function ShowCaptcha()
    {
        return CaptchaHelper::G()->doShowCaptcha();
    }
    public static function CheckCaptcha($captcha)
    {
        return CaptchaHelper::G()->doCheckCaptcha($captcha);
    }
    protected function doCheckPermission()
    {
        $path_info = static::getPathInfo();
        $admin = SessionBusiness::G()->getCurrentAdmin();
        $flag = AdminBusiness::G()->checkPermission($admin,$path_info);
        
        return $flag; // 我们抛出异常得了。 
    }
}
