<?php declare(strict_types=1);
/**
 * DuckAdmin DemoUsers - UserAction
 * 极简回调类:由 GlobalUser 以 user_callback_* 回调调用(不实现 UserActionInterface)
 * 登录态基于 Session + 预设用户数组
 */
namespace DuckAdmin\DemoUsers\Controller;

use DuckPhp\Core\App;
use DuckPhp\Foundation\SingletonTrait;
use DuckPhp\GlobalUser\UserServiceInterface;

use DuckAdmin\DemoUsers\Business\UserService;

class UserAction
{
    public function login($post)
    {
        Helper::FireGlobalEvent(Helper::$EVENT_ACTION_LOGINING,$post);

        $username = (string)($post['username']??'');
        $password = (string)($post['password']??'');

        $user = UserBuseness::_()->verifyLogin($username, $password);

        if ($user) {
            Session::_()->setCurrentUser($user);
            Helper::FireGlobalEvent(Helper::$EVENT_ACTION_LOGINED,$post);
        }
        return $user;
    }

    public function logout()
    {
        $user_id = Helper::UserId(false);
        Helper::FireGlobalEvent(Helper::$EVENT_ACTION_LOGOUTING, $user_id);
        Session::_()->unsetCurrentUser();
        Helper::FireGlobalEvent(Helper::$EVENT_ACTION_LOGOUTED, $user_id);
    }
}