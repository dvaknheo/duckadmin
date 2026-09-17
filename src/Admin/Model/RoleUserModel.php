<?php declare(strict_types=1);
/**
 * DuckPhp Admin System - RoleUser Model
 * 用户-角色关系管理
 */
namespace DuckAdmin\Admin\Model;

class RoleUserModel extends Base
{
    /**
     * 获取用户的职位ID（1对1）
     */
    public function getUserRoleId(int $userId): ?int
    {
        $sql = "SELECT role_id FROM admin_role_users WHERE user_id = ?";
        return (int)$this->fetchColumn($sql, [$userId]) ?: null;
    }

    /**
     * 设置用户职位（1对1）
     */
    public function setUserRole(int $userId, ?int $roleId): void
    {
        $sql = "DELETE FROM admin_role_users WHERE user_id = ?";
        $this->execute($sql, [$userId]);
        if ($roleId !== null) {
            $sql = "INSERT INTO admin_role_users (user_id, role_id) VALUES (?, ?)";
            $this->execute($sql, [$userId, $roleId]);
        }
    }
}
