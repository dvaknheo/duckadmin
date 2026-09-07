<?php declare(strict_types=1);
/**
 * DuckAdmin DemoUsers - 预设用户系统
 * 不使用数据库、不使用安装系统;登录限定 demo_users 数组中的用户
 */
namespace DuckAdmin\DemoUsers\System;

use DuckPhp\Foundation\Controller\Helper;
use DuckPhp\GlobalUser\GlobalUser;

class GlobalUserEx extends GlobalUser
{
    public $options = [
        'user_callback_for_login_service' => null,
        'user_callback_for_login_session' => null,
    ];
    public function __construct()
    {
        $this->options = array_replace_recursive($this->options, (new parent())->options); //merge parent's options;
        parent::__construct();
    }
    public function id(bool $check_login = true)
    {
        $id = $this->getLoginSession()->getCurrentUserId();
        Helper::ThrowOn($check_login && !$id,"NoLogin", -1, \Excetpion::class);
        return $id ?? 0;
    }
    public function name(bool $check_login = true): string
    {
        $name = $this->getLoginSession()->getCurrentUserName();
        Helper::ThrowOn($check_login && !$name,"NoLogin", -2, \UserExcetpion::class);
        return $name;
    }

    public function data(bool $check_login = true): array
    {
        if ($check_login) {
            $this->id(true);
        }
        return $this->getLoginSession()->getCurrentUser();
    }
    protected function getLoginBusiness()
    {
        $callback = $this->options['user_callback_for_login_service'];
        return ($callback)();
    }
    protected function getLoginSession()
    {
        $callback = $this->options['user_callback_for_login_session'];
        return ($callback)();
    }
    public function regist($post)
    {
        Helper::FireGlobalEvent(Helper::$EVENT_ACTION_REGISTING, $post);
        $user = $this->getLoginBusiness()->regist($post);
        $this->getLoginSession()->setCurrentUser($user);
        Helper::FireGlobalEvent(Helper::$EVENT_ACTION_REGISTED, $post);
        Helper::Show302(__url($this->options['user_url_home']));
    }

    public function login($post)
    {
        Helper::FireGlobalEvent(Helper::$EVENT_ACTION_LOGINING, $post);
        $user = $this->getLoginBusiness()->login($post);
        $this->getLoginSession()->setCurrentUser($user);
        Helper::FireGlobalEvent(Helper::$EVENT_ACTION_LOGINED, $post);
        Helper::Show302(__url($this->options['user_url_home']));
    }
    public function logout()
    {
        $user_id = Helper::UserId(false);
        Helper::FireGlobalEvent(Helper::$EVENT_ACTION_LOGOUTING, $user_id);
        $this->getLoginSession()->unsetCurrentUser();
        Helper::FireGlobalEvent(Helper::$EVENT_ACTION_LOGOUTED, $user_id);
        Helper::Show302(__url($this->options['user_url_login']));
    }
    public function isLogin()
    {
        return (bool)$this->getLoginSession()->getCurrentUser();
    }
}