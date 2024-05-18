<?php declare(strict_types=1);
/**
 * DuckPhp
 * From this time, you never be alone~
 */
namespace DuckAdmin\System;

use DuckPhp\Component\GlobalAdmin;
use DuckPhp\Core\Phase;
use DuckAdmin\Controller\AdminAction;
use DuckAdmin\Controller\Helper;

class Admin extends GlobalAdmin
{
    //
    public function current()
    {
        return AdminAction::CallInPhase(App::Phase());
    }
    public function id()
    {
        return AdminAction::_()->getCurrentAdminId();
    }
    public function data()
    {
        return AdminAction::_()->getCurrentAdmin();
    }
    public function canAccessUrl($url)
    {
        $this->checkLogin();
        return true;
    }
    public function canAccessCall($class, $method)
    {
        return AdminAction::_()->checkAccess($class,$method);
    }
    public function checkLogin()
    {
        return AdminAction::_()->checkLogin();
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
