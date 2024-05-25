<?php declare(strict_types=1);
/**
 * DuckPhp
 * From this time, you never be alone~
 */
namespace DuckAdmin\System;

use DuckPhp\Component\GlobalAdmin;
use DuckAdmin\Controller\AdminAction;
use DuckAdmin\Controller\Helper;

class Admin extends GlobalAdmin
{
    //
    public function action()
    {
        //override
        return AdminAction::_()->action();
    }
    public function service()
    {
        //override
        return null;
        //return AdminService::_()->();
    }
    public function current()
    {
        return AdminAction::CallInPhase(App::Phase());
    }
    public function id()
    {
        return AdminAction::_()->getCurrentAdmin()['id'];
    }
    public function data()
    {
        return AdminAction::_()->getCurrentAdmin();
    }
    public function checkAccess($controller, $action,$url = null)
    {
        return AdminAction::_()->checkAccess($controller, $action, $url);
    }
    public function checkLogin()
    {
        return AdminAction::_()->getCurrentAdmin();
    }
    ///////////////
    public function urlForRegist($url_back = null, $ext = null)
    {
        return __url("");
    }
    public function urlForLogin($url_back = null, $ext = null)
    {
        return __url("");
    }
    public function urlForLogout($url_back = null, $ext = null)
    {
        return __url("account/logout");
    }
}
