<?php declare(strict_types=1);
/**
 * DuckAdmin DemoUsers - Session 管理(极简)
 */
namespace DuckAdmin\DemoUsers\Controller;

use DuckPhp\Foundation\SessionTrait;

class Session
{
    use SessionTrait;
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

    public function getCurrentUser(): array
    {
        return $this->get('user', []);
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
