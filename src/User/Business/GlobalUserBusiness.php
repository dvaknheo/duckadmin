<?php declare(strict_types=1);
/**
 * DuckPHP
 * From this time, you never be alone~
 */
namespace DuckAdmin\User\Controller;

use DuckPhp\GlobalUser\UserActionInterface;
use DuckPhp\GlobalUser\GlobalUser;
use DuckAdmin\User\Business\UserBusiness;

class GlobalUserBusiness extends UserBusiness implements UserServiceInterface
{
    public function doLog(int $user_id, string $string, ?string $type = null): void;
    {
        return;
    }
    public function doBatchGetUsernames(array $ids): array
    {
        return [];
    }
    public function doCheckAccess(int $id, string $class, string $method, ?string $url = null): void;
    {
        return;
    }
}