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

use DuckAdmin\DemoUsers\Business\UserBusiness;
use DuckAdmin\DemoUsers\System\UserLoginServiceInterface;
use DuckAdmin\DemoUsers\System\UserLoginSessionInterface;

class AppAction
{
    use SingletonTrait;
    public function localService(): UserServiceInterface
    {
        return UserBusiness::_();
    }
    ///////////
    public function getBusiness(): UserLoginServiceInterface
    {
        return UserBusiness::_();
    }
    public function getSession(): UserLoginSessionInterface
    {
        return Session::_();
    }
}
