<?php declare(strict_types=1);
/**
 * DuckAdmin SingleAdmin - AdminService(极简:单管理员,无鉴权)
 */
namespace DuckAdmin\SingleAdmin\Business;

use DuckPhp\Foundation\SingletonTrait;
use DuckPhp\GlobalAdmin\AdminServiceInterface;

class AdminBusiness implements AdminServiceInterface
{
    use SingletonTrait;

    public function canAccess($admin_id, string $class, string $method, ?string $url = null): bool
    {
        return true;
    }

    public function log($admin_id, string $string, ?string $type = null, array $ext = [])
    {
        return;
    }

    public function isSuper($admin_id): bool
    {
        return true;
    }
    ///////////////
    public function login(array $post): ?array
    {
        Helper::FireGlobalEvent(Helper::$EVENT_LOGINING);

        $password = (string)($post['password']??'');
        $old_password = Helper::AppOptions('single_admin_password', '');

        Helper::ThrowOn($password !== $old_password, "密码错误");

        $admin = [
            'id'=>1,
            'name'=>'Admin',
        ];
        Helper::FireGlobalEvent(Helper::$EVENT_LOGINED, $admin);
        return $admin;
    }
    public function logout($id)
    {
        Helper::FireGlobalEvent(Helper::$EVENT_LOGINING);
        Helper::FireGlobalEvent(Helper::$EVENT_LOGIOUT);
    }
}
