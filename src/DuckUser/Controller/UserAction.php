<?php declare(strict_types=1);
/**
 * DuckPHP
 * From this time, you never be alone~
 */
namespace DuckUser\Controller;

use DuckPhp\GlobalUser\UserActionInterface;
use DuckPhp\Core\App;

use DuckUser\Business\UserBusiness;

class UserAction extends Base
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
        Helper::ControllerThrowOn(!$user, '请登录', -1, UserException::class);
        $this->user = $user;
        return $this->user;
    }
    public function id($check_login = true)
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
    public function data(array $post): array
    {
        return Session::_()->getCurrentUser();
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
        Session::_()->unsetCurrentUser();
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