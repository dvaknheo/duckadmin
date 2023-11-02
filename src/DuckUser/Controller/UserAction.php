<?php declare(strict_types=1);
/**
 * DuckPHP
 * From this time, you never be alone~
 */
namespace DuckUser\Controller;

use DuckPhp\Foundation\SimpleControllerTrait;
use DuckUser\Business\UserBusiness;

class UserAction
{
    use SimpleControllerTrait;
    
    public function id()
    {
        $user = Session::_()->getCurrentUser();
        Helper::ThrowOn(!$user, '请登录');
        return $user['id'];
    }
    public function data()
    {
        return Session::_()->getCurrentUser();
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
}