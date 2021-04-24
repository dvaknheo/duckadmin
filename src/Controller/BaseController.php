<?php declare(strict_types=1);
/**
 * DuckPhp
 * From this time, you never be alone~
 */

namespace DuckAdmin\Controller;

use DuckAdmin\App\BaseController as Base;
use DuckAdmin\App\ControllerHelper as C;
use DuckAdmin\Service\AdminService;
use DuckAdmin\Service\SessionService;
use DuckAdmin\Service\ServciceException;

class BaseController extends Base
{
    public function __construct()
    {
        if (static::class === self::class) {
            C::Exit404();
        }
        parent::__construct();
    }
    protected function initialize()
    {
        C::assignExceptionHandler(ServciceException::class, function(){
            C::ExitRouteTo('login?r='.C::Server('PATH_INFO'));
        });
        $admin = SessionService::G()->getCurrentAdmin();
        //App::getRouteCallingMethod();
        $path_info = C::Server('PATH_INFO');
        // 我们把路径拿过来
        $flag = AdminService::G()->checkPermission($admin,$path_info); // 这里还要对改名后的处理呢，怎么办？
        if(!$flag){
            C::Exit404();
            return;
        }
        $menu = AdminService::G()->getMenu($admin['id']);
        C::assignViewData('menu', $menu);
        parent::initialize();
    }
}
