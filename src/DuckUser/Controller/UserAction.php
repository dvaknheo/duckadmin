<?php declare(strict_types=1);
/**
 * DuckPHP
 * From this time, you never be alone~
 */
namespace DuckUser\Controller;

use DuckUser\Business\UserBusiness;

class UserAction extends Base
{
    protected $user  =[];
    public function current()
    {
        $user = Session::_()->getCurrentUser();
        ControllerException::ThrowOn(!$user, '请登录');
        
        return $this;
    }
    public function id()
    {
        return $this->user['id'];
    }
    public function data()
    {
        return $this->user;
    }
    public function register($post)
    {
        $user = UserBusiness::G()->register($post);
        Session::_()->setCurrentUser($user);
    }
    public function login()
    {
        $user = UserBusiness::G()->login($post);
        Session::_()->setCurrentUser($user);
    }
    public function logout()
    {
        Session::_()->unsetCurrentUser();
    }
    ///////////////////
    public function urlForRegist($url_back = null, $ext = null)
    {
        return __url('regist');
    }
    public function urlForLogin($url_back = null, $ext = null)
    {
        return __url('login');
    }
    public function urlForLogout($url_back = null, $ext = null)
    {
        return __url('logout');
    }
    public function urlForHome($url_back = null, $ext = null)
    {
        return __url('home/index');
    }
    
}