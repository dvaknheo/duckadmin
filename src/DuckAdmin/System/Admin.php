<?php declare(strict_types=1);
/**
 * DuckPhp
 * From this time, you never be alone~
 */
namespace DuckAdmin\System;

use DuckPhp\Component\GlobalAdmin;
use DuckPhp\Core\Phase;
use DuckAdmin\Controller\AdminAction;

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
    
    public function checkLogin()
    {
       return AdminAction::_()->checkLogin();
    }
    ///////////////
    public function urlForRegist($url_back = null, $ext = null)
    {
        throw new \ErrorException('DuckPhp: No Impelement');
    }
    public function urlForLogin($url_back = null, $ext = null)
    {
        throw new \ErrorException('DuckPhp: No Impelement');
    }
    public function urlForLogout($url_back = null, $ext = null)
    {
        throw new \ErrorException('DuckPhp: No Impelement');
    }
    ///////////////
    public function regist($post)
    {
        throw new \ErrorException('DuckPhp: No Impelement');
    }
    public function login($post)
    {
        throw new \ErrorException('DuckPhp: No Impelement');
    }
    public function logout()
    {
        throw new \ErrorException('DuckPhp: No Impelement');
    }
    
}
