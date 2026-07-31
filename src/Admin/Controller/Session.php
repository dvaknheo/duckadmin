<?php declare(strict_types=1);
/**
 * DuckPhp Admin System - Session 管理
 */
namespace DuckAdmin\Admin\Controller;

use DuckPhp\Foundation\SessionTrait;

class Session
{
    use SessionTrait;
    
   
    /**
     * 是否已登录
     */
    public function isLogin(): bool
    {
        return $this->get('user_id') > 0;
    }
    
    /**
     * 获取当前用户 ID
     */
    public function getUserId(): ?int
    {
        $id = $this->get('user_id');
        return $id !== null ? (int)$id : null;
    }
    
    /**
     * 获取当前用户名
     */
    public function getUsername(): ?string
    {
        return $this->get('username');
    }
    
    /**
     * 获取当前用户姓名
     */
    public function getRealname(): ?string
    {
        return $this->get('realname');
    }
    
    /**
     * 登录成功后设置 Session
     */
    public function setLogin(int $id, string $username, string $realname): void
    {
        $this->set('user_id', $id);
        $this->set('username', $username);
        $this->set('realname', $realname);
    }
    
    /**
     * 清除 Session（退出登录）
     */
    public function logout(): void
    {
        $this->unset('user_id');
        $this->unset('username');
        $this->unset('realname');
        session_destroy();
    }
}
