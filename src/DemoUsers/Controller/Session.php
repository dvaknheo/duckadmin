<?php declare(strict_types=1);
/**
 * DuckAdmin DemoUsers - Session 管理(极简)
 */
namespace DuckAdmin\DemoUsers\Controller;

use DuckPhp\Foundation\SessionTrait;
use DuckPhp\Foundation\Controller\UserSessionTrait;
use DuckPhp\GlobalUser\UserSessionInterface;

class Session implements UserSessionInterface
{
    use SessionTrait;
    use UserSessionTrait;
    public function getCurrentUserId()
    {
        $user = $this->get('user', []);
        return $user['id'] ?? 0;
    }
    public function getCurrentUserName(): string
    {
        $user = $this->get('user', []);
        return $user['name'] ?? '';
    }
}
