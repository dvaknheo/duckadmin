<?php declare(strict_types=1);
/**
 * DuckPhp Admin System - RolePermission Model
 * admin_role_permissions 表操作
 */
namespace DuckAdmin\Admin\Model;

class RolePermissionModel extends Base
{
    /**
     * 获取角色拥有的权限ID列表
     */
    public function getRolePermissionIds(int $roleId): array
    {
        $sql = "SELECT permission_id FROM admin_role_permissions WHERE role_id = ?";
        $rows = $this->fetchAll($sql, [$roleId]);
        return array_column($rows, 'permission_id');
    }

    /**
     * 设置角色的权限
     */
    public function setRolePermissions(int $roleId, array $permissionIds): void
    {
        $sql = "DELETE FROM admin_role_permissions WHERE role_id = ?";
        $this->execute($sql, [$roleId]);
        foreach ($permissionIds as $permId) {
            $sql = "INSERT INTO admin_role_permissions (role_id, permission_id) VALUES (?, ?)";
            $this->execute($sql, [$roleId, (int)$permId]);
        }
    }

    /**
     * 检查角色是否有指定权限
     */
    public function hasPermission(int $roleId, int $permissionId): bool
    {
        $sql = "SELECT 1 FROM admin_role_permissions WHERE role_id = ? AND permission_id = ? LIMIT 1";
        $exists = $this->fetch($sql, [$roleId, $permissionId]);
        return !empty($exists);
    }
}
