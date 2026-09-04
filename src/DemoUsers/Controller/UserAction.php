<?php declare(strict_types=1);
/**
 * DuckAdmin DemoUsers - UserAction
 * 极简回调类:由 GlobalUser 以 user_callback_* 回调调用(不实现 UserActionInterface)
 * 登录态基于 Session + 预设用户数组
 */
namespace DuckAdmin\DemoUsers\Controller;

use DuckPhp\Foundation\SingletonTrait;
use DuckAdmin\DemoUsers\Business\UserBusiness;

class UserAction
{
    use SingletonTrait;
    public function login($post)
    {
        Helper::FireGlobalEvent(Helper::$EVENT_ACTION_LOGINING,$post);

        $user = UserBusiness::_()->login($post);
        Session::_()->setCurrentUser($user);
        Helper::FireGlobalEvent(Helper::$EVENT_ACTION_LOGINED,$post);
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