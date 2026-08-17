<?php declare(strict_types=1);
/**
 * DuckAdmin SingleAdmin - AdminAction
 * 极简 GlobalAdmin 替代:让 Helper::Admin() 可用(实现 AdminActionInterface)
 * 不使用数据库;登录态基于 Session;single_admin_password 由 SingleAdminApp 配置
 */
namespace DuckAdmin\SingleAdmin\Controller;

use DuckPhp\Core\App;
use DuckPhp\Core\View;
use DuckPhp\Foundation\SingletonTrait;
use DuckPhp\GlobalAdmin\AdminActionInterface;
use DuckPhp\GlobalAdmin\AdminServiceInterface;

use DuckAdmin\SingleAdmin\Business\AdminService;

class AdminAction implements AdminActionInterface
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
        $id = Session::_()->getUserId();
        if ($check_login && !$id) {
            Helper::Show302(__url(App::_()->options['admin_url_login'] ?? ''));
            Helper::exit();
        }
        return $id ?? 0;
    }

    public function name(bool $check_login = true): string
    {
        $name = Session::_()->getUsername() ?? '';
        if ($check_login && $name === '') {
            Helper::Show302(__url(App::_()->options['admin_url_login'] ?? ''));
            Helper::exit();
        }
        return $name;
    }

    public function data(bool $check_login = true): array
    {
        if ($check_login) {
            $this->id(true);
        }
        return [];
    }

    public function service(): AdminServiceInterface
    {
        return AdminService::_();
    }

    public function localService(): AdminServiceInterface
    {
        return AdminService::_();
    }



    ////////////////// urls

    protected function urlFor(string $key): string
    {
        return __url((string)(App::_()->options[$key] ?? ''));
    }

    public function urlForLogin(?string $url_back = null, ?array $ext = null): string
    {
        return $this->urlFor('admin_url_login');
    }

    public function urlForLogout(?string $url_back = null, ?array $ext = null): string
    {
        return $this->urlFor('admin_url_logout');
    }

    public function urlForHome(?string $url_back = null, ?array $ext = null): string
    {
        return $this->urlFor('admin_url_home');
    }

    ////////////////// view

    public function addExtViewData(array $input): array
    {
        $input['__logined_id'] ??= $this->id(false);
        $input['__logined_name'] ??= $this->name(false);
        $input['__logined_url_logout'] ??= $this->urlForLogout();
        return $input;
    }

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
        return (bool)Session::_()->getUserId();
    }

    public function log(string $string, ?string $type = null, array $ext = [])
    {
        return;
    }

    public function isSuper(): bool
    {
        return true;
    }
}
