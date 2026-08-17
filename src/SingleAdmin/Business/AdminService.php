<?php declare(strict_types=1);
/**
 * DuckAdmin SingleAdmin - AdminService(极简:单管理员,无鉴权)
 */
namespace DuckAdmin\SingleAdmin\Business;

use DuckPhp\Foundation\SingletonTrait;
use DuckPhp\GlobalAdmin\AdminServiceInterface;

class AdminService implements AdminServiceInterface
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
}
