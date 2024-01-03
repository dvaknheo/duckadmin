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
    public function __construct()
    {
        // must override me
    }
    public function checkLogin()
    {
    }
    public function current()
    {
        $user = Session::_()->getCurrentUser();
        ControllerException::ThrowOn(!$user, '请登录');
        $this->user = $user;
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
        $user = UserBusiness::_()->register($post);
        Session::_()->setCurrentUser($user);
    }
    public function login($post)
    {
        $user = UserBusiness::_()->login($post);
        Session::_()->setCurrentUser($user);
    }
    public function logout()
    {
        Session::_()->unsetCurrentUser();
    }
    public function getUsernames($ids)
    {
        return UserBusiness::_()->getUsernames($ids);
    }
    ///////////////////
    public function urlForRegist($url_back = null, $ext = null)
    {
        return __url('register');
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
        return __url('Home/index');
    }
    
}