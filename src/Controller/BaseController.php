<?php declare(strict_types=1);
/**
 * DuckPhp
 * From this time, you never be alone~
 */

namespace DuckAdmin\Controller;

use DuckAdmin\App\BaseController as Base;
use DuckAdmin\App\SingletonExTrait;
use DuckAdmin\App\ControllerHelper as C;

use DuckAdmin\Service\AdminService;
use DuckAdmin\Service\SessionService;
use DuckAdmin\Service\ServciceException;

class BaseController
{
    use SingletonExTrait;

    public function __construct()
    {
        if (static::class === self::class) {
            //禁止直接访问
            C::Exit404();
        }
        $this->initialize();
    }
    protected function initialize()
    {
        C::assignExceptionHandler(ServciceException::class, function(){
            C::ExitRouteTo('login?r=' . C::getPathInfo());
        });
        
        $path_info = C::getPathInfo();
        $admin = SessionService::G()->getCurrentAdmin();
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
        C::setViewHeadFoot('header','footer');
    }
}
