<?php declare(strict_types=1);
/**
 * DuckPhp
 * From this time, you never be alone~
 */

namespace DuckAdmin\App;

use DuckPhp\Helper\ControllerHelperTrait;
use DuckPhp\SingletonEx\SingletonExTrait;

use DuckAdmin\App\ControllerHelper as C;
use DuckAdmin\Service\AdminService;
use DuckAdmin\Service\SessionService;

/**
 * 这是充当 Helper 助手的 控制器基类
 * 这里要注意的是，控制器的公开动态方法都会当成 web 动作，所以尽量避免公开动态方法
 */
class BaseController
{
    use SingletonExTrait;
    use ControllerHelperTrait;
    
    public function __construct()
    {
    }
    protected function initialize()
    {
    }
    protected function initViewData($admin, $path_info)
    {
        $menu = AdminService::G()->getMenu($admin['id'],$path_info);
        C::assignViewData('menu', $menu);
        C::assignViewData('admin', $admin);
        C::setViewHeadFoot('header','footer');
    }
    //////////////////
    
    public static function CheckPerMission()
    {
        $admin = SessionService::G()->getCurrentAdmin();
        $flag = AdminService::G()->checkPermission($admin,$path_info);
        
        return $flag;
    }
    protected function CheckCapthca($captcha)
    {
        $builder = new CaptchaBuilder();
        $flag = PhraseBuilder::comparePhrases($_SESSION['phrase']??'', captcha);
    }
    public function CaptchaBuilder()
    {
        $phraseBuilder = new PhraseBuilder(4, '0123456789');
        $builder = new CaptchaBuilder(null, $phraseBuilder);
        $builder->build()->output();        
        $_SESSION['phrase'] = $builder->getPhrase();
    }
    public static function CheckPerMission()
    {
        $admin = SessionService::G()->getCurrentAdmin();
        $flag = AdminService::G()->checkPermission($admin,$path_info);
        
        return $flag;
    }
    protected function CheckCapthca($captcha)
    {
        $phrase = SessionService::G()->getPhrase($phrase);
        $builder = new CaptchaBuilder();
        $flag = PhraseBuilder::comparePhrases($phrase, captcha);
    }
    public function CaptchaBuilder()
    {
        $phraseBuilder = new PhraseBuilder(4, '0123456789');
        $builder = new CaptchaBuilder(null, $phraseBuilder);
        $builder->build()->output();
        $phrase = $builder->getPhrase();
        
        SessionService::G()->setPhrase($phrase);
    }
}
