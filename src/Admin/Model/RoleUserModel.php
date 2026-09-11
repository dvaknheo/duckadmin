<?php declare(strict_types=1);
/**
 * DuckPhp Admin System - RoleUser Model
 * 用户-角色关系管理
 */
namespace DuckAdmin\Admin\Model;

class RoleUserModel extends Base
{
    public function getUserRoleIds(int $userId): array
    {
        $sql = "SELECT role_id FROM admin_role_users WHERE user_id = ?";
        $rows = $this->fetchAll($sql, [$userId]);
        return array_column($rows, 'role_id');
    }

    public function setUserRoles(int $userId, array $roleIds): void
    {
        $sql = "DELETE FROM admin_role_users WHERE user_id = ?";
        $this->execute($sql, [$userId]);
        foreach ($roleIds as $roleId) {
            $sql = "INSERT INTO admin_role_users (user_id, role_id) VALUES (?, ?)";
            $this->execute($sql, [$userId, (int)$roleId]);
        }
    }

    public function isSuperRole(int $userId): bool
    {
        $sql = "SELECT r.id FROM admin_roles r
                INNER JOIN admin_role_users ru ON r.id = ru.role_id
                WHERE ru.user_id = ? AND r.deleted_at IS NULL AND r.is_super = 1";
        $row = $this->fetch($sql, [$userId]);
        return !empty($row);
    }
}
