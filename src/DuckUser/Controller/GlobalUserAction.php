<?php declare(strict_types=1);
/**
 * DuckPHP
 * From this time, you never be alone~
 */
namespace DuckUser\Controller;

use DuckPhp\GlobalUser\GlobalUserTrait;
use DuckPhp\GlobalUser\UserActionInterface;

use DuckUser\Business\GlobalUserBusiness;

use DuckUser\Controller\UserAction;

class GlobalUserAction implements UserActionInterface
{
    use  GlobalUserTrait;
    public function localService()
    {
        return GlobalUserBusiness::_();
    }
    //
    public function getHeaderFooterData(array $input): array
    {
        return [
            'header' => '',
            'footer' => '',
        ];
    }
    public function id($check_login = true)
    {
        return UserAction::_()->id($check_login);
    }
    public function name($check_login = true):string
    {
        return UserAction::_()->name($check_login);
    }

    public function login(array $post): array
    {
        $user = UserBusiness::_()->login($post);
        Session::_()->setCurrentUser($user);
        return $user;
    }
    public function logout(): void
    {
        Session::_()->unsetCurrentUser();
    }
    public function regist(array $post): array
    {
        $user = UserBusiness::_()->register($post);
        Session::_()->setCurrentUser($user);
        return $user;
    }
    ///////////////
    public function batchGetUsernames($ids)
    {
        return UserBusiness::_()->batchGetUsernames($ids);
    }
    
    public function urlForRegist($url_back = null, $ext = null):string
    {
        return __url('register');
    }
    public function urlForLogin($url_back = null, $ext = null):string
    {
        return __url($url_back? "login?b=".__url($url_back):"login");
    }
    public function urlForLogout($url_back = null, $ext = null):string
    {
        return __url('logout');
    }
    public function urlForHome($url_back = null, $ext = null):string
    {
        return __url(App::_()->options['home_url']);
    }
    
}