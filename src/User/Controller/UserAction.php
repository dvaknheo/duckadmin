<?php declare(strict_types=1);
/**
 * DuckPHP
 * From this time, you never be alone~
 */
namespace DuckAdmin\User\Controller;

use DuckPhp\GlobalUser\UserActionInterface;
use DuckPhp\Core\App;

use DuckAdmin\User\Business\UserBusiness;

class UserAction extends Base
{
    protected $user = null;
    public function __construct()
    {
        // just override for skip init;
    }
    public function id($check_login = true)
    {
        $user = Session::_()->getCurrentUser();
        $id = $user['id'] ?? null;
        Helper::ControllerThrowOn($check_login && !$id, '请登录', -1, UserException::class);
        return (int)$id;
    }
    public function name($check_login = true):string
    {
        $user = Session::_()->getCurrentUser();
        $name = $user['name'] ?? null ;
        Helper::ControllerThrowOn($check_login &&!$user, '请登录', -1, UserException::class);
        return (string)$name;
    }
    public function service()
    {
        return UserBusiness::_();
    }
    public function login(array $post)
    {
        $user = UserBusiness::_()->login($post);
        Session::_()->setCurrentUser($user);
    }
    public function logout()
    {
        $id = $this->id(false);
        Session::_()->unsetCurrentUser();
        Helper::fireGlobalEvent('logout', $id);
    }
    public function regist(array $post)
    {
        $user = UserBusiness::_()->register($post);
        Session::_()->setCurrentUser($user);
        return $user;
    }
    ///////////////
    public function urlForHome()
    {
        return App::_()->options['home_url'];
    }
}