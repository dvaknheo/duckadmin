<?php declare(strict_types=1);
/**
 * DuckAdmin SingleAdmin - AdminAction
 * 极简回调类:由 GlobalAdmin 以 admin_callback_* 回调调用(不实现 AdminActionInterface)
 * 不使用数据库;登录态基于 Session;single_admin_password 由 SingleAdminApp 配置
 */
namespace DuckAdmin\SingleAdmin\Controller;

use DuckPhp\Core\App;
use DuckPhp\Foundation\SingletonTrait;
use DuckPhp\GlobalAdmin\AdminServiceInterface;

use DuckAdmin\SingleAdmin\Business\AdminService;

class AdminAction
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

    public function localService(): AdminServiceInterface
    {
        return AdminService::_();
    }


}
