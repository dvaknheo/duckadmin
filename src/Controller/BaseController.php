<?php declare(strict_types=1);
/**
 * DuckPhp
 * From this time, you never be alone~
 */

namespace DuckAdmin\Controller;

use DuckAdmin\App\BaseController as DuckAdminController;
use DuckAdmin\App\BaseController as C;
use DuckAdmin\Service\SessionService;
use DuckAdmin\Service\AdminService;
use DuckPhp\Core\App;
// 我们这里只是偷懒一下啦。 等价于 ControllerHelper
//这里和业务相关，严格禁止第三方 
class BaseController extends DuckAdminController
{
    public function __construct()
    {
        // 我们弄个小技巧，不允许直接访问，但我们可以创建一个实例填充，
        if (self::class === App::Route()->getRouteCallingClass()) {
            //__var_dump(App::Route()->getRouteCallingClass());
            //禁止直接访问
            C::Exit404();
            return;
        }
        if(self::class === static::class){
            // 作为助手模式。 
            return;
        }
        $this->initialize();
    }
    protected function initialize()
    {
        C::assignExceptionHandler(\Exception::class, function(){
            // 这里应该调整成可调的
            C::ExitRouteTo('login?r=' . C::getPathInfo());
        });
        
        $admin = SessionService::G()->getCurrentAdmin();
        $path_info = C::getPathInfo();
        $flag = AdminService::G()->checkPermission($admin,$path_info);
        
        if(!$flag){
            C::Exit404();
            return;
        }
        ///////////////// 正常流程
        $this->initViewData($admin, $path_info);
    }
    protected function initViewData($admin, $path_info)
    {
        $menu = AdminService::G()->getMenu($admin['id'],$path_info);
        C::assignViewData('menu', $menu);
        C::assignViewData('admin', $admin);
        C::setViewHeadFoot('header','footer');
    }
    //////////////////
/*
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
*/
}
