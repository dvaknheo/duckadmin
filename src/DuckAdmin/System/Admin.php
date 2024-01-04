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
use DuckAdmin\Controller\ControllerException;

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
        // 这里如果没登录，我们跳转到 报错页面
        Helper::assignExceptionHandler(ControllerException::class,function(){
            Helper::ExitRedirect(__url('index')); // TODO backurl
        });
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
        return __url("logout");
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
    public function logout($post)
    {
        throw new \ErrorException('DuckPhp: No Impelement');
    }
    
}
