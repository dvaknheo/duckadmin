<?php declare(strict_types=1);
/**
 * DuckAdmin DemoUsers - UserAction
 * 让 Helper::User() 可用(实现 UserActionInterface);登录态基于 Session + 预设用户数组
 */
namespace DuckAdmin\DemoUsers\Controller;

use DuckPhp\Core\App;
use DuckPhp\Core\View;
use DuckPhp\Foundation\SingletonTrait;
use DuckPhp\GlobalUser\UserActionInterface;
use DuckPhp\GlobalUser\UserServiceInterface;

use DuckAdmin\DemoUsers\Business\UserService;

class UserAction implements UserActionInterface
{
    use SingletonTrait;

    public $options = [];

    public function init(array $options, ?object $context = null)
    {
        $this->options = $options;
        return $this;
    }

    //////////////////

    public function id(bool $check_login = true)
    {
        $user = Session::_()->getCurrentUser();
        $id = $user['id'] ?? null;
        if ($check_login && !$id) {
            Helper::Show302(__url($this->options['user_url_login'] ?? ''));
            Helper::exit();
        }
        return $id ?? 0;
    }

    public function name(bool $check_login = true): string
    {
        $user = Session::_()->getCurrentUser();
        $name = $user['name'] ?? '';
        if ($check_login && $name === '') {
            Helper::Show302(__url($this->options['user_url_login'] ?? ''));
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

    public function service(): UserServiceInterface
    {
        return UserService::_();
    }
    ////////////////// urls

    protected function urlFor(string $key): string
    {
        return __url((string)($this->options[$key] ?? ''));
    }

    public function urlForRegist(?string $url_back = null, ?array $ext = null): string
    {
        return $this->urlFor('user_url_regist');
    }

    public function urlForLogin(?string $url_back = null, ?array $ext = null): string
    {
        return $this->urlFor('user_url_login');
    }

    public function urlForLogout(?string $url_back = null, ?array $ext = null): string
    {
        return $this->urlFor('user_url_logout');
    }

    public function urlForHome(?string $url_back = null, ?array $ext = null): string
    {
        return $this->urlFor('user_url_home');
    }

    ////////////////// view

    public function mergeViewData(array $input): array
    {
        $input['__logined_id'] ??= $this->id(false);
        $input['__logined_name'] ??= $this->name(false);
        $input['__logined_url_logout'] ??= $this->urlForLogout();
        return $input;
    }

    public function show(array $data = [], string $view = ''): void
    {
        $data = $this->mergeViewData($data);
        View::_()->_Show($data, $view);
    }

    //////////////////

    public function canAccess(?string $class = null, ?string $method = null, ?string $url = null): bool
    {
        return (bool)Session::_()->getCurrentUser()['id'] ?? false;
    }

    public function log(string $string, ?string $type = null, array $ext = [])
    {
        return;
    }

    public function batchGetUsernames(array $ids): array
    {
        return UserService::_()->batchGetUsernames($ids);
    }
}
