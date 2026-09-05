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

class AppAction
{
    use SingletonTrait;

    //////////////////

    public function id(bool $check_login = true)
    {
        $user = Session::_()->getCurrentUser();
        $id = $user['id'] ?? null;
        if ($check_login && !$id) {
            Helper::Show302(__url(App::_()->options['user_url_login'] ?? ''));
            Helper::exit();
        }
        return $id ?? 0;
    }

    public function name(bool $check_login = true): string
    {
        $user = Session::_()->getCurrentUser();
        $name = $user['username'] ?? '';
        if ($check_login && $name === '') {
            Helper::Show302(__url(App::_()->options['user_url_login'] ?? ''));
            Helper::exit();
        }
        return $name;
    }

    public function data(bool $check_login = true): array
    {
        if ($check_login) {
            $this->id(true);
        }
        return Session::_()->getCurrentUser();
    }

    public function localService(): UserServiceInterface
    {
        return UserBusiness::_();
    }
    ///////////    
}
