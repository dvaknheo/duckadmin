<?php declare(strict_types=1);
/**
 * DuckPhp
 * From this time, you never be alone~
 */

namespace DuckAdmin\System;

use DuckPhp\Helper\ControllerHelperTrait;
use DuckPhp\SingletonEx\SingletonExTrait;

use DuckAdmin\Service\SessionService;
use DuckAdmin\Service\AdminService;

use Gregwar\Captcha\CaptchaBuilder;
use Gregwar\Captcha\PhraseBuilder;

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
        
        $admin = SessionService::G()->getCurrentAdmin();
        $path_info = static::getPathInfo();
        $flag = AdminService::G()->checkPermission($admin,$path_info);
        
        if(!$flag){
            static::Exit404();
            return;
        }
        ///////////////// 正常流程
        $this->initViewData($admin, $path_info);
    }
    public static function CheckLocalController($self,$static)
    {
        if ($self === $static) {
            if ($self === DuckAdmin::Route()->getRouteCallingClass()) {
                //禁止直接访问
                static::Exit404();
            }
            // 作为助手调用。
            return true;
        }
        return false;
    }
    //////// 提供给第三方用的静态方法 ////////

    public static function ShowCaptcha()
    {
        return static::G()->doShowCaptcha();
    }
    public static function CheckCaptcha($captcha)
    {
        return static::G()->doCheckCaptcha($captcha);
    }
    
    public static function CheckPermission()
    {
        $admin = SessionService::G()->getCurrentAdmin();
        $flag = AdminService::G()->checkPermission($admin,$path_info);
        
        return $flag;
    }    
    //////// 验证码部分 ////////
    public function doShowCaptcha()
    {
        $phraseBuilder = new PhraseBuilder(4, '0123456789');
        $builder = new CaptchaBuilder(null, $phraseBuilder);
        $builder->build();
        
        $phrase = $builder->getPhrase();
        SessionService::G()->setPhrase($phrase);
        
        static::header('Content-type: image/jpeg');
        static::header('Cache-Control: no-cache, no-store, max-age=0, must-revalidate');
        $builder->output();
    }
    protected function doCheckCaptcha($captcha)
    {
        $builder = new CaptchaBuilder();
        $phrase = SessionService::G()->getPhrase();
        $flag = PhraseBuilder::comparePhrases($phrase, $captcha);
        return $flag;
    }
    //////// 业务逻辑部分 ////////
    
    protected function doCheckPermission($path_info)
    {
        $admin = SessionService::G()->getCurrentAdmin();
        $flag = AdminService::G()->checkPermission($admin,$path_info);
        
        return $flag;
    }
    //////// 和视图相关的部分 ////////
    protected function initViewData($admin, $path_info)
    {
        $menu = AdminService::G()->getMenu($admin['id'],$path_info);
        static::assignViewData('menu', $menu);
        static::assignViewData('admin', $admin);
        static::setViewHeadFoot('header','footer');
    }
    //////// 助手方法部分 ////////
}
