<?php declare(strict_types=1);
/**
 * DuckAdmin SingleAdmin - AdminService(极简:单管理员,无鉴权)
 */
namespace DuckAdmin\SingleAdmin\Business;

use DuckPhp\Foundation\Business\Helper;
use DuckPhp\Foundation\SingletonTrait;
use DuckPhp\GlobalAdmin\AdminServiceInterface;
use DuckPhp\GlobalAdmin\GlobalAdmin;


class AdminBusiness implements AdminServiceInterface
{
    use SingletonTrait;

    public function canAccess($admin_id, string $class, string $method, ?string $url = null): bool
    {
        return true;
    }

    /**
     * Summary of log
     * @param mixed $admin_id
     * @param string $str
     * @param mixed $type
     * @param array $ext
     * @return void
     */
    public function log($admin_id, string $str, ?string $type = null, array $ext = []) //@codeCoverageIgnore
    {
        return;
    }

    public function isSuper($admin_id): bool
    {
        return true;
    }
    ///////////////
    public function login(array $post): array
    {
        Helper::FireGlobalEvent(GlobalAdmin::EVENT_SERVICE_ADMIN_LOGINING);

        $password = (string)($post['password']??'');
        $old_password = Helper::AppOptions('single_admin_password', '');

        Helper::ThrowOn($password !== $old_password, "密码错误");

        $admin = [
            'id'=>1,
            'name'=>'Admin',
        ];
        Helper::FireGlobalEvent(GlobalAdmin::EVENT_SERVICE_ADMIN_LOGINED);
        return $admin;
    }
    public function logout($id)
    {
        Helper::FireGlobalEvent(GlobalAdmin::EVENT_SERVICE_ADMIN_LOGOUTING);
        Helper::FireGlobalEvent(GlobalAdmin::EVENT_SERVICE_ADMIN_LOGOUTED);
    }
}
