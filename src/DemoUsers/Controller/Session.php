<?php declare(strict_types=1);
/**
 * DuckAdmin DemoUsers - Session 管理(极简)
 */
namespace DuckAdmin\DemoUsers\Controller;

use DuckPhp\Foundation\SessionTrait;

class Session
{
    use SessionTrait;

    public function getCurrentUser(): array
    {
        return $this->get('user', []);
    }

    public function isLogin(): bool
    {
        $user = $this->getCurrentUser();
        return !empty($user['id']);
    }

    public function setCurrentUser(array $user): void
    {
        $this->set('user', $user);
    }

    public function unsetCurrentUser(): void
    {
        $this->set('user', []);
    }
}
