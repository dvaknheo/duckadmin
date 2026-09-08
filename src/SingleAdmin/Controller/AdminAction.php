<?php declare(strict_types=1);
/**
 * DuckAdmin SingleAdmin - AdminAction
 * 极简回调类:由 GlobalAdmin 以 admin_callback_* 回调调用(不实现 AdminActionInterface)
 * 不使用数据库;登录态基于 Session;single_admin_password 由 SingleAdminApp 配置
 */
namespace DuckAdmin\SingleAdmin\Controller;

use DuckPhp\Foundation\SingletonTrait;
use DuckPhp\GlobalAdmin\AdminServiceInterface;

use DuckAdmin\SingleAdmin\Business\AdminService;

class AdminAction
{
    use SingletonTrait;

    public function localService(): AdminServiceInterface
    {
        return AdminService::_();
    }
    public function session()
    {
        return Session::_();
    }
}
