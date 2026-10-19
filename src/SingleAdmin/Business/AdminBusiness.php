<?php declare(strict_types=1);
/**
 * DuckAdmin SingleAdmin - AdminService(极简:单管理员,无鉴权)
 */
namespace DuckAdmin\SingleAdmin\Business;

use DuckPhp\Foundation\Business\BusinessHelper as Helper;
use DuckPhp\Foundation\SingletonTrait;
use DuckPhp\GlobalAdmin\AdminServiceInterface;

class AdminBusiness implements AdminServiceInterface
{
    use SingletonTrait;

    /**
     * 参数顺序与 DuckPhp\GlobalAdmin\AdminServiceInterface::canAccess 一致($url 在第二位)
     * @param int|string $admin_id
     */
    public function canAccess($admin_id, ?string $url = null, ?string $class = null, ?string $method = null): bool
    {
        return true;
    }

    /**
     * 参数名与 DuckPhp\GlobalAdmin\AdminServiceInterface::log($admin_id, string $string, ...) 对齐
     * (原来第2个参数叫 $str;覆盖率 DSL 按参数名传参,名字不一致会取不到值)
     * @param mixed $admin_id
     * @param mixed $type
     * @param array<string, mixed> $ext
     * @return void
     */
    public function log($admin_id, string $string, ?string $type = null, array $ext = []) //@codeCoverageIgnore
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
        Helper::FireGlobalEvent(Helper::EVENT_SERVICE_ADMIN_LOGINING);

        $password = (string)($post['password']??'');
        $old_password = Helper::AppOptions('single_admin_password', '');

        Helper::ThrowOn(empty($password), "系统尚未开放");
        Helper::ThrowOn($password !== $old_password, "密码错误");

        $admin = [
            'id'=>1,
            'name'=>'Admin',
        ];
        Helper::FireGlobalEvent(Helper::EVENT_SERVICE_ADMIN_LOGINED);
        return $admin;
    }
    public function logout($id)
    {
        Helper::FireGlobalEvent(Helper::EVENT_SERVICE_ADMIN_LOGOUTING);
        Helper::FireGlobalEvent(Helper::EVENT_SERVICE_ADMIN_LOGOUTED);
    }
}
