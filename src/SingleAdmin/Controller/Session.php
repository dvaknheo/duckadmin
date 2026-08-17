<?php declare(strict_types=1);
/**
 * DuckAdmin SingleAdmin - Session 管理(极简)
 */
namespace DuckAdmin\SingleAdmin\Controller;

use DuckPhp\Foundation\SessionTrait;

class Session
{
    use SessionTrait;

    public function isLogin(): bool
    {
        return $this->get('user_id') > 0;
    }

    public function getUserId(): ?int
    {
        $id = $this->get('user_id');
        return $id !== null ? (int)$id : null;
    }

    public function getUsername(): ?string
    {
        return $this->get('username');
    }

    /**
     * 登录成功:固定 admin
     */
    public function setLogin(string $username): void
    {
        $this->set('user_id', 1);
        $this->set('username', $username);
    }

    public function logout(): void
    {
        $this->unset('user_id');
        $this->unset('username');
        session_destroy();
    }
}
