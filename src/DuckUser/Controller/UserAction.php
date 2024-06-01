<?php declare(strict_types=1);
/**
 * DuckPHP
 * From this time, you never be alone~
 */
namespace DuckUser\Controller;

use DuckPhp\GlobalUser\UserActionInterface;

use DuckUser\Business\UserBusiness;

class UserAction extends Base implements UserActionInterface
{
    protected $user = null;
    public function __construct()
    {
        // must override me
    }
    public function current()
    {
        if ($this->user) {
            return $this->user;
        }
        $user = Session::_()->getCurrentUser();
        Helper::ControllerThrowOn(!$user, '请登录');
        $this->user = $user;
        return $this->user;
    }
    public function id($check_login = true):int
    {
        if($check_login){
            return (int)$this->current()['id'];
        }
        try{
            return (int)$this->current()['id'];
        }catch(\Exception $ex){
            return 0;
        }
    }
    public function name($check_login = true):string
    {
        if($check_login){
            return $this->current()['username'];
        }
        try{
            return $this->current()['username'];
        }catch(\Exception $ex){
            return '';
        }
    }
    public function service()
    {
        return UserBusiness::_Z();
    }
    public function login(array $post)
    {
        $user = UserBusiness::_()->login($post);
        Session::_()->setCurrentUser($user);
    }
    public function logout()
    {
        Session::_()->unsetCurrentUser();
    }
    public function regist(array $post)
    {
        $user = UserBusiness::_()->register($post);
        Session::_()->setCurrentUser($user);
    }
    ///////////////
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
        return __url('Home/index');
    }
    public function urlForRegist($url_back = null, $ext = null):string
    {
        return __url('register');
    }
    public function batchGetUsernames($ids)
    {
        return UserBusiness::_()->batchGetUsernames($ids);
    }
    
}