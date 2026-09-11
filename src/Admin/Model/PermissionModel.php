<?php declare(strict_types=1);
/**
 * DuckPhp Admin System - Permission Model
 */
namespace DuckAdmin\Admin\Model;

class PermissionModel extends Base
{
    public function getAll(): array
    {
        $sql = "SELECT * FROM admin_permissions WHERE deleted_at IS NULL ORDER BY weight ASC, id ASC";
        return $this->fetchAll($sql);
    }

    public function getById(int $id): ?array
    {
        $sql = "SELECT * FROM admin_permissions WHERE id = ? AND deleted_at IS NULL";
        $ret = $this->fetch($sql, [$id]);
        return $ret === false ? null : $ret;
    }

    public function getPageList(int $page, int $pageSize, string $search = ''): array
    {
        $where = "deleted_at IS NULL";
        $params = [];
        if ($search !== '') {
            $where .= " AND (name LIKE ? OR url LIKE ?)";
            $like = '%' . $search . '%';
            $params = [$like, $like];
        }
        $totalSql = "SELECT COUNT(*) as total FROM admin_permissions WHERE {$where}";
        $total = $this->fetch($totalSql, $params)['total'] ?? 0;
        $offset = ($page - 1) * $pageSize;
        $listSql = "SELECT * FROM admin_permissions WHERE {$where} ORDER BY weight ASC, id ASC LIMIT ? OFFSET ?";
        $listParams = array_merge($params, [$pageSize, $offset]);
        $list = $this->fetchAll($listSql, $listParams);
        return ['total' => (int)$total, 'list' => $list];
    }

    public function create(array $data): int
    {
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['updated_at'] = date('Y-m-d H:i:s');
        $sql = "INSERT INTO admin_permissions (name, url, type, parent_id, weight, source, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $this->execute($sql, [
            $data['name'], $data['url'] ?? '', $data['type'] ?? 1,
            $data['parent_id'] ?? 0, $data['weight'] ?? 0,
            $data['source'] ?? 0,
            $data['created_at'], $data['updated_at']
        ]);
        return (int)$this->lastInsertId();
    }

    public function edit(int $id, array $data): bool
    {
        $data['updated_at'] = date('Y-m-d H:i:s');
        $sql = "UPDATE admin_permissions SET name = ?, url = ?, type = ?, parent_id = ?, weight = ?, source = ?, updated_at = ? WHERE id = ?";
        $this->execute($sql, [
            $data['name'], $data['url'] ?? '', $data['type'] ?? 1,
            $data['parent_id'] ?? 0, $data['weight'] ?? 0,
            $data['source'] ?? 0,
            $data['updated_at'], $id
        ]);
        return true;
    }

    public function delete(int $id): bool
    {
        $now = date('Y-m-d H:i:s');
        $sql = "UPDATE admin_permissions SET deleted_at = ?, updated_at = ? WHERE id = ?";
        $this->execute($sql, [$now, $now, $id]);
        return true;
    }

    public function getRolePermissionIds(int $roleId): array
    {
        $sql = "SELECT permission_id FROM admin_role_permissions WHERE role_id = ?";
        $rows = $this->fetchAll($sql, [$roleId]);
        return array_column($rows, 'permission_id');
    }

    public function setRolePermissions(int $roleId, array $permissionIds): void
    {
        $sql = "DELETE FROM admin_role_permissions WHERE role_id = ?";
        $this->execute($sql, [$roleId]);
        foreach ($permissionIds as $permId) {
            $sql = "INSERT INTO admin_role_permissions (role_id, permission_id) VALUES (?, ?)";
            $this->execute($sql, [$roleId, (int)$permId]);
        }
    }

    public function grantAllPermissions(int $roleId): void
    {
        $sql = "SELECT id FROM admin_permissions WHERE deleted_at IS NULL";
        $rows = $this->fetchAll($sql);
        $ids = array_column($rows, 'id');
        $this->setRolePermissions($roleId, $ids);
    }

    /**
     * 按名称查询目录(type=0) id,不存在返回 0
     */
    public function getDirIdByName(string $name): int
    {
        return $this->findDirectoryId($name, 0);
    }

    /**
     * 按名称+父级查询目录(type=0) id,不存在返回 0
     * 分组(顶级) parentId=0;目录 parentId=所属分组 id
     */
    public function findDirectoryId(string $name, int $parentId): int
    {
        $row = $this->fetch(
            "SELECT id FROM admin_permissions WHERE type = 0 AND name = ? AND parent_id = ? AND deleted_at IS NULL",
            [$name, $parentId]
        );
        return $row ? (int)$row['id'] : 0;
    }

    /**
     * 获取所有权限ID（供Service层使用）
     */
    public function getAllIds(): array
    {
        $sql = "SELECT id FROM admin_permissions WHERE deleted_at IS NULL";
        $rows = $this->fetchAll($sql);
        return array_column($rows, 'id');
    }

    /**
     * 获取用户在角色中拥有的权限ID（供Service层使用）
     */
    public function getUserPermissionIdsByRoles(int $userId): array
    {
        $sql = "SELECT DISTINCT p.id FROM admin_permissions p
                INNER JOIN admin_role_permissions rp ON p.id = rp.permission_id
                INNER JOIN admin_role_users ru ON rp.role_id = ru.role_id
                WHERE ru.user_id = ? AND p.deleted_at IS NULL";
        $rows = $this->fetchAll($sql, [$userId]);
        return array_column($rows, 'id');
    }

    /**
     * 获取用户拥有的菜单项（type 0/1）（供Service层使用）
     */
    public function getMenuItemsByUser(int $userId): array
    {
        $sql = "SELECT DISTINCT p.id, p.name, p.url, p.type, p.parent_id, p.weight
                FROM admin_permissions p
                INNER JOIN admin_role_permissions rp ON p.id = rp.permission_id
                INNER JOIN admin_role_users ru ON rp.role_id = ru.role_id
                WHERE ru.user_id = ? AND p.deleted_at IS NULL AND p.type IN (0,1)
                ORDER BY p.weight ASC, p.id ASC";
        return $this->fetchAll($sql, [$userId]);
    }

    /**
     * 获取所有菜单项（type 0/1）（供Service层使用）
     */
    public function getAllMenuItems(): array
    {
        $sql = "SELECT id, name, url, type, parent_id, weight FROM admin_permissions
                WHERE deleted_at IS NULL AND type IN (0,1)
                ORDER BY weight ASC, id ASC";
        return $this->fetchAll($sql);
    }

    /**
     * 检查用户是否有指定URL的权限（供Service层使用）
     */
    public function countUserUrlPermissions(int $userId, string $path): int
    {
        $sql = "SELECT COUNT(*) FROM admin_permissions p
                INNER JOIN admin_role_permissions rp ON p.id = rp.permission_id
                INNER JOIN admin_role_users ru ON rp.role_id = ru.role_id
                WHERE ru.user_id = ? AND p.deleted_at IS NULL AND p.url = ?";
        return (int)$this->fetchColumn($sql, [$userId, $path]);
    }

}
