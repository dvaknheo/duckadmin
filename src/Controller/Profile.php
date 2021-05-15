<?php declare(strict_types=1);
/**
 * DuckPhp
 * From this time, you never be alone~
 */

namespace DuckAdmin\Controller;
use DuckAdmin\App\ControllerHelper as C;
use DuckAdmin\Service\AdminService;
use DuckAdmin\Service\SessionService;

class Profile extends BaseController
{
    public function __construct()
    {
        parent::__construct();
    }
    /**
     * 首页
     */ 
    public function index()
    {
        C::Show(get_defined_vars(),'profile/home');
    }
    //修改密码
    public function pass()
    {
    }

}
